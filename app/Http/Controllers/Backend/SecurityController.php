<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AuditLog;
use App\Models\CashClosing;
use App\Models\Invoice;
use App\Models\InvoiceCancelRequest;
use App\Models\InvoicePayment;
use App\Models\LoginHistory;
use App\Models\Setting;
use App\Services\SecurityService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SecurityController extends Controller
{
    private const DISMISSED_SETTING = 'suspicious_report_dismissed';

    /** Keeps the JSON inside the settings.value TEXT column. */
    private const DISMISSED_LIMIT = 2500;

    private const RESETS_SETTING = 'suspicious_report_resets';

    public function __construct(private SecurityService $security)
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
    }

    public function settings()
    {
        $this->authorizeSecurity('security.settings');
        $branchId = (int) auth()->user()->branch_id;

        $values = [];
        foreach (array_keys(SecurityService::DEFAULTS) as $key) {
            $values[$key] = $this->security->setting($key, $branchId);
        }

        return view('backend.pages.security.settings', [
            'values' => $values,
            'ownerPhones' => $this->security->ownerPhones($branchId),
            'smsBalance' => (float) (\App\Models\SmsBalance::where('branch_id', $branchId)->value('balance') ?? 0),
            'tableReady' => $this->security->tablesReady(),
        ]);
    }

    public function updateSettings(Request $request)
    {
        $this->authorizeSecurity('security.settings');

        $yesNo = 'required|in:Yes,No';
        $validated = $request->validate([
            'security_owner_phones' => 'nullable|string|max:255',
            'security_alert_delete' => $yesNo,
            'security_alert_edit_reduce' => $yesNo,
            'security_alert_discount_percent' => 'required|numeric|min:0|max:100',
            'security_alert_cancel_request' => $yesNo,
            'security_alert_cash_short' => $yesNo,
            'security_daily_summary' => $yesNo,
            'security_patient_bill_sms' => $yesNo,
            'security_payment_sms' => $yesNo,
            'security_invoice_lock_hours' => 'required|integer|min:0|max:720',
            'security_require_reason' => $yesNo,
            'security_cancel_approval' => $yesNo,
        ]);

        $invalid = collect(preg_split('/[\s,;]+/', (string) ($validated['security_owner_phones'] ?? '')))
            ->filter()
            ->reject(fn ($phone) => formatPhoneNumber($phone));
        if ($invalid->isNotEmpty()) {
            return RedirectHelper::backWithInputFromException('<strong>Sorry!!!</strong> ভুল মোবাইল নম্বর: ' . e($invalid->implode(', ')));
        }

        $validated['security_owner_phones'] = collect(preg_split('/[\s,;]+/', (string) ($validated['security_owner_phones'] ?? '')))
            ->map(fn ($phone) => formatPhoneNumber($phone))
            ->filter()->unique()->implode(',');

        $this->security->saveSettings($validated, (int) auth()->user()->branch_id);

        return RedirectHelper::back('<strong>Saved!</strong> Security settings update হয়েছে।');
    }

    public function testAlert()
    {
        $this->authorizeSecurity('security.settings');
        $branchId = (int) auth()->user()->branch_id;

        if (empty($this->security->ownerPhones($branchId))) {
            return RedirectHelper::backWithInputFromException('<strong>Sorry!!!</strong> আগে Owner-এর মোবাইল নম্বর সেট করুন।');
        }

        $this->security->alertOwners($branchId, 'Test alert from ' . $this->security->companyName($branchId) . ' software. Security SMS is working.');

        return RedirectHelper::back('<strong>Sent!</strong> Test SMS পাঠানো হয়েছে। না পেলে SMS balance চেক করুন।');
    }

    public function report(Request $request)
    {
        $this->authorizeSecurity('security.report');
        $branchId = (int) auth()->user()->branch_id;
        $today = Carbon::now(SecurityService::TIMEZONE);
        $start = $request->filled('start_date') ? Carbon::parse($request->start_date)->toDateString() : $today->copy()->subDays(6)->toDateString();
        $end = $request->filled('end_date') ? Carbon::parse($request->end_date)->toDateString() : $today->toDateString();
        if ($start > $end) {
            [$start, $end] = [$end, $start];
        }
        [$fromUtc] = $this->security->utcDayRange($start);
        [, $toUtc] = $this->security->utcDayRange($end);
        $threshold = (float) $this->security->setting('security_alert_discount_percent', $branchId);

        $dismissed = $this->dismissedItems($branchId);
        $resets = $this->reportResets($branchId);
        $showHidden = $request->boolean('show_hidden');
        $activeResets = $showHidden ? ['all' => null, 'staff' => []] : $resets;
        $beforeReset = function ($adminId, $time) use ($resets): bool {
            $cutoff = $this->resetCutoff($resets, $adminId);
            return $cutoff !== null && (!$time || Carbon::parse($time)->format('Y-m-d H:i:s') <= $cutoff);
        };

        $auditReady = Schema::hasTable('audit_logs');
        $invoiceLogs = $auditReady
            ? AuditLog::with('admin')->where('branch_id', $branchId)->where('module', 'invoice')->whereBetween('created_at', [$fromUtc, $toUtc])->get()
            : collect();

        $staff = $this->staffSummary(
            $branchId, $start, $end, $fromUtc, $toUtc,
            $showHidden ? $invoiceLogs : $invoiceLogs->reject(fn ($log) => $beforeReset($log->admin_id, $log->created_at))->values(),
            $activeResets
        );

        $highDiscounts = Invoice::with('admin')
            ->where('branch_id', $branchId)
            ->whereBetween('creation_date', [$start, $end])
            ->where('discount_amount', '>', 0)
            ->whereRaw('discount_amount * 100 >= ? * (total_amount + discount_amount)', [$threshold > 0 ? $threshold : 100])
            ->orderByDesc('discount_amount')
            ->limit(300)
            ->get();

        $lateEdits = $invoiceLogs
            ->where('action', 'updated')
            ->filter(function ($log) {
                $created = $log->old_values['creation_date'] ?? null;
                return $created && $log->created_at->timezone(SecurityService::TIMEZONE)->toDateString() > Carbon::parse($created)->toDateString();
            })
            ->sortByDesc('created_at')
            ->values();

        $reducedEdits = $invoiceLogs
            ->where('action', 'updated')
            ->filter(fn ($log) => (float) ($log->new_values['total_amount'] ?? 0) < (float) ($log->old_values['total_amount'] ?? 0))
            ->sortByDesc('created_at')
            ->values();

        $hiddenCount = 0;
        $hiddenKeys = [];
        $visible = function (Collection $items, callable $key) use ($dismissed, $showHidden, $beforeReset, &$hiddenCount, &$hiddenKeys) {
            $hidden = $items->filter(fn ($item) => isset($dismissed[$key($item)]) || $beforeReset($item->admin_id, $item->created_at));
            $hiddenCount += $hidden->count();
            foreach ($hidden as $item) {
                $hiddenKeys[$key($item)] = true;
            }

            return $showHidden ? $items : $items->reject(fn ($item) => isset($hiddenKeys[$key($item)]))->values();
        };
        $highDiscounts = $visible($highDiscounts, fn ($invoice) => 'i' . $invoice->id)->take(50)->values();
        $lateEdits = $visible($lateEdits, fn ($log) => 'a' . $log->id);
        $reducedEdits = $visible($reducedEdits, fn ($log) => 'a' . $log->id);
        $gaps = $this->invoiceGaps($branchId, $start, $end, $auditReady, $dismissed, $showHidden, $hiddenCount, $hiddenKeys, $resets, $beforeReset);

        $failedLogins = 0;
        if ($this->security->tablesReady()) {
            $failedQuery = LoginHistory::where('branch_id', $branchId)->whereIn('event', [LoginHistory::EVENT_FAILED, LoginHistory::EVENT_LOCKED])
                ->whereBetween('created_at', [$fromUtc, $toUtc]);
            $this->applyResets($failedQuery, 'admin_id', 'created_at', $activeResets);
            $failedLogins = $failedQuery->count();
        }

        return view('backend.pages.security.report', [
            'start' => $start,
            'end' => $end,
            'threshold' => $threshold,
            'staff' => $staff,
            'highDiscounts' => $highDiscounts,
            'lateEdits' => $lateEdits,
            'reducedEdits' => $reducedEdits,
            'gaps' => $gaps,
            'dismissed' => $dismissed,
            'hiddenKeys' => $hiddenKeys,
            'resets' => $resets,
            'resetAdmins' => Admin::whereIn('id', collect($resets['staff'])->keys()->merge(collect($resets['staff'])->pluck('by'))->push($resets['all']['by'] ?? null)->filter())
                ->pluck('name', 'id'),
            'showHidden' => $showHidden,
            'hiddenCount' => $hiddenCount,
            'failedLogins' => $failedLogins,
            'auditReady' => $auditReady,
            'tableReady' => $this->security->tablesReady(),
        ]);
    }

    public function dismissReportItems(Request $request)
    {
        $this->authorizeSecurity('security.report');
        $request->validate([
            'keys' => 'required|array|max:200',
            'keys.*' => ['required', 'string', 'regex:/^(a\d+|i\d+|g\d{4}-\d{2}-\d+)$/'],
            'mode' => 'required|in:hide,restore',
        ]);
        $branchId = (int) auth()->user()->branch_id;

        $dismissed = $this->dismissedItems($branchId);
        foreach ($request->keys as $key) {
            if ($request->mode === 'hide') {
                $dismissed[$key] ??= (int) auth()->id();
            } else {
                unset($dismissed[$key]);
            }
        }
        Setting::updateOrCreate(
            ['branch_id' => $branchId, 'key' => self::DISMISSED_SETTING],
            ['value' => json_encode(array_slice($dismissed, -self::DISMISSED_LIMIT, null, true))]
        );
        Log::info('Suspicious report items ' . $request->mode, ['branch_id' => $branchId, 'keys' => count($request->keys), 'by' => auth()->id()]);

        return RedirectHelper::back($request->mode === 'hide'
            ? '<strong>Removed!</strong> Report থেকে সরানো হয়েছে। Trash/Audit-এর রেকর্ড আগের মতোই আছে।'
            : '<strong>Restored!</strong> আবার report-এ দেখাবে।');
    }

    /**
     * @return array<string, int> item key => admin id who removed it
     */
    private function dismissedItems(int $branchId): array
    {
        $value = Setting::where('branch_id', $branchId)->where('key', self::DISMISSED_SETTING)->value('value');
        $items = json_decode((string) $value, true);

        return is_array($items) ? $items : [];
    }

    public function resetReport(Request $request)
    {
        $this->authorizeSecurity('security.report');
        $request->validate([
            'mode' => 'required|in:all,staff,undo_all,undo_staff',
            'admin_id' => 'required_if:mode,staff,undo_staff|nullable|integer',
        ]);
        $branchId = (int) auth()->user()->branch_id;
        $resets = $this->reportResets($branchId);
        $adminId = (int) $request->admin_id;
        $stamp = ['at' => now()->format('Y-m-d H:i:s'), 'by' => (int) auth()->id()];

        if (in_array($request->mode, ['staff', 'undo_staff'], true)
            && !Admin::where('id', $adminId)->where('branch_id', $branchId)->exists()) {
            return RedirectHelper::backWithInputFromException('<strong>Sorry!!!</strong> Staff পাওয়া যায়নি।');
        }

        switch ($request->mode) {
            case 'all':
                $resets = ['all' => $stamp, 'staff' => []];
                $message = '<strong>Cleared!</strong> এখন পর্যন্ত সব কিছু report থেকে সরানো হয়েছে। Invoice, payment, Trash/Audit কিছুই মোছেনি।';
                break;
            case 'staff':
                $resets['staff'][$adminId] = $stamp;
                $message = '<strong>Cleared!</strong> এই staff-এর এখন পর্যন্ত সব হিসাব report থেকে সরানো হয়েছে। আসল data আগের মতোই আছে।';
                break;
            case 'undo_all':
                $resets['all'] = null;
                $message = '<strong>Restored!</strong> পুরো report clear তুলে দেওয়া হয়েছে।';
                break;
            default:
                unset($resets['staff'][$adminId]);
                $message = '<strong>Restored!</strong> এই staff-এর হিসাব আবার report-এ দেখাবে।';
        }

        Setting::updateOrCreate(
            ['branch_id' => $branchId, 'key' => self::RESETS_SETTING],
            ['value' => json_encode($resets)]
        );
        Log::info('Suspicious report reset', ['branch_id' => $branchId, 'mode' => $request->mode, 'staff_id' => $adminId ?: null, 'by' => auth()->id()]);

        return RedirectHelper::back($message);
    }

    /**
     * @return array{all: ?array{at: string, by: int}, staff: array<int, array{at: string, by: int}>}
     */
    private function reportResets(int $branchId): array
    {
        $value = json_decode((string) Setting::where('branch_id', $branchId)->where('key', self::RESETS_SETTING)->value('value'), true);
        $all = $value['all'] ?? null;
        $staff = array_filter((array) ($value['staff'] ?? []), fn ($reset) => is_array($reset) && !empty($reset['at']));

        return [
            'all' => is_array($all) && !empty($all['at']) ? $all : null,
            'staff' => $staff,
        ];
    }

    private function resetCutoff(array $resets, $adminId): ?string
    {
        $times = array_filter([$resets['all']['at'] ?? null, $adminId ? ($resets['staff'][(int) $adminId]['at'] ?? null) : null]);

        return $times ? max($times) : null;
    }

    /**
     * Keeps only rows newer than the report reset of the whole branch and of the row's staff.
     */
    private function applyResets($query, string $adminColumn, string $timeColumn, array $resets): void
    {
        $all = $resets['all']['at'] ?? null;
        if ($all) {
            $query->where($timeColumn, '>', $all);
        }
        foreach ($resets['staff'] as $adminId => $reset) {
            if ($all && $reset['at'] <= $all) {
                continue;
            }
            $query->where(fn ($q) => $q->whereNull($adminColumn)
                ->orWhere($adminColumn, '!=', (int) $adminId)
                ->orWhere($timeColumn, '>', $reset['at']));
        }
    }

    public function logins(Request $request)
    {
        $this->authorizeSecurity('security.logins');
        $branchId = (int) auth()->user()->branch_id;
        $tableReady = $this->security->tablesReady();

        $logins = collect();
        if ($tableReady) {
            $query = LoginHistory::with('admin')
                ->where(function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                    if (auth()->user()->hasRole('Super Admin')) {
                        $q->orWhereNull('branch_id');
                    }
                });
            if ($request->filled('event')) {
                $query->where('event', $request->event);
            }
            if ($request->filled('admin_id')) {
                $query->where('admin_id', $request->admin_id);
            }
            if ($request->filled('date')) {
                $query->whereBetween('created_at', $this->security->utcDayRange(Carbon::parse($request->date)->toDateString()));
            }
            $logins = $query->latest('created_at')->paginate(50)->withQueryString();
        }

        return view('backend.pages.security.logins', [
            'logins' => $logins,
            'tableReady' => $tableReady,
            'admins' => Admin::where('branch_id', $branchId)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    private function staffSummary(int $branchId, string $start, string $end, $fromUtc, $toUtc, Collection $invoiceLogs, array $resets): Collection
    {
        $invoiceQuery = Invoice::where('branch_id', $branchId)->whereBetween('creation_date', [$start, $end]);
        $this->applyResets($invoiceQuery, 'admin_id', 'created_at', $resets);
        $invoiceStats = $invoiceQuery
            ->groupBy('admin_id')
            ->selectRaw('admin_id, COUNT(*) as bills, SUM(total_amount) as total, SUM(discount_amount) as discount')
            ->get()->keyBy('admin_id');

        $paymentQuery = InvoicePayment::where('branch_id', $branchId)->whereBetween('creation_date', [$start, $end]);
        $this->applyResets($paymentQuery, 'admin_id', 'created_at', $resets);
        $collections = $paymentQuery
            ->groupBy('admin_id')
            ->selectRaw('admin_id, SUM(paid_amount) as collected')
            ->pluck('collected', 'admin_id');

        $cancelRequests = collect();
        $cashShort = collect();
        if ($this->security->tablesReady()) {
            $cancelQuery = InvoiceCancelRequest::where('branch_id', $branchId)->whereBetween('created_at', [$fromUtc, $toUtc]);
            $this->applyResets($cancelQuery, 'requested_by', 'created_at', $resets);
            $cancelRequests = $cancelQuery
                ->groupBy('requested_by')->selectRaw('requested_by, COUNT(*) as total')
                ->pluck('total', 'requested_by');
            $closingQuery = CashClosing::where('branch_id', $branchId)
                ->whereBetween('closing_date', [$start, $end])->where('difference', '<', 0);
            $this->applyResets($closingQuery, 'admin_id', 'created_at', $resets);
            $cashShort = $closingQuery
                ->groupBy('admin_id')->selectRaw('admin_id, SUM(difference) as short')
                ->pluck('short', 'admin_id');
        }

        $adminIds = $invoiceStats->keys()
            ->merge($collections->keys())
            ->merge($invoiceLogs->pluck('admin_id'))
            ->merge($cancelRequests->keys())
            ->merge($cashShort->keys())
            ->filter()->unique();
        $admins = Admin::whereIn('id', $adminIds)->get()->keyBy('id');

        return $adminIds->map(function ($adminId) use ($admins, $invoiceStats, $collections, $invoiceLogs, $cancelRequests, $cashShort) {
            $stats = $invoiceStats->get($adminId);
            $logs = $invoiceLogs->where('admin_id', $adminId);
            $gross = (float) ($stats->total ?? 0) + (float) ($stats->discount ?? 0);
            $reduced = $logs->where('action', 'updated')->sum(
                fn ($log) => max(0, (float) ($log->old_values['total_amount'] ?? 0) - (float) ($log->new_values['total_amount'] ?? 0))
            );
            $row = [
                'admin' => $admins->get($adminId),
                'bills' => (int) ($stats->bills ?? 0),
                'total' => (float) ($stats->total ?? 0),
                'discount' => (float) ($stats->discount ?? 0),
                'discount_percent' => $gross > 0 ? round(((float) ($stats->discount ?? 0) / $gross) * 100, 1) : 0,
                'collected' => (float) ($collections[$adminId] ?? 0),
                'edits' => $logs->where('action', 'updated')->count(),
                'deletes' => $logs->where('action', 'deleted')->count(),
                'reduced' => $reduced,
                'cancel_requests' => (int) ($cancelRequests[$adminId] ?? 0),
                'cash_short' => abs((float) ($cashShort[$adminId] ?? 0)),
            ];
            $row['risk'] = $row['deletes'] * 3 + $row['cancel_requests'] * 2 + $row['edits']
                + ($row['reduced'] > 0 ? 3 : 0) + ($row['cash_short'] > 0 ? 3 : 0) + ($row['discount_percent'] >= 20 ? 2 : 0);

            return $row;
        })->sortByDesc('risk')->values();
    }

    /**
     * Invoice numbers restart every month (INV-0001), so gaps are checked month by month.
     */
    private function invoiceGaps(int $branchId, string $start, string $end, bool $auditReady, array $dismissed, bool $showHidden,
                                 int &$hiddenCount, array &$hiddenKeys, array $resets, callable $beforeReset): array
    {
        $gaps = [];
        $months = CarbonPeriod::create(Carbon::parse($start)->startOfMonth(), '1 month', Carbon::parse($end)->startOfMonth());
        $resetAll = $resets['all']['at'] ?? null;

        foreach ($months as $month) {
            if (count($gaps) >= 12) {
                break;
            }

            $invoices = Invoice::where('branch_id', $branchId)
                ->whereYear('creation_date', $month->year)
                ->whereMonth('creation_date', $month->month)
                ->get(['invoice_number', 'created_at'])
                ->map(fn ($invoice) => [
                    'number' => (int) preg_replace('/\D/', '', (string) $invoice->invoice_number),
                    'created_at' => $invoice->created_at?->format('Y-m-d H:i:s'),
                ])
                ->filter(fn ($invoice) => $invoice['number'] > 0);
            $numbers = $invoices->pluck('number')->unique();

            if ($numbers->isEmpty()) {
                continue;
            }

            $monthKey = 'g' . $month->format('Y-m') . '-';
            $missing = collect(range(1, $numbers->max()))->diff($numbers)->values();
            if ($missing->isEmpty()) {
                continue;
            }

            $deletedLogs = $auditReady
                ? AuditLog::with('admin')->where('branch_id', $branchId)->where('module', 'invoice')->where('action', 'deleted')
                    ->where('created_at', '>=', $month->copy()->subDay())
                    ->get()
                    ->filter(function ($log) use ($month) {
                        $date = $log->old_values['creation_date'] ?? null;
                        return $date && Carbon::parse($date)->format('Y-m') === $month->format('Y-m');
                    })
                    ->keyBy(fn ($log) => (int) preg_replace('/\D/', '', (string) ($log->old_values['invoice_number'] ?? '')))
                : collect();

            $lastNumberAtReset = $resetAll
                ? (int) $invoices->filter(fn ($invoice) => $invoice['created_at'] && $invoice['created_at'] <= $resetAll)->max('number')
                : 0;
            $hidden = $missing->filter(function ($number) use ($dismissed, $monthKey, $deletedLogs, $beforeReset, $lastNumberAtReset) {
                if (isset($dismissed[$monthKey . $number])) {
                    return true;
                }
                $log = $deletedLogs->get($number);

                return $log ? $beforeReset($log->admin_id, $log->created_at) : $number <= $lastNumberAtReset;
            });
            $hiddenCount += $hidden->count();
            foreach ($hidden as $number) {
                $hiddenKeys[$monthKey . $number] = true;
            }
            if (!$showHidden) {
                $missing = $missing->diff($hidden)->values();
            }
            if ($missing->isEmpty()) {
                continue;
            }

            $gaps[] = [
                'month' => $month->format('F Y'),
                'last' => $numbers->max(),
                'missing' => $missing->take(200)->map(fn ($number) => [
                    'key' => $monthKey . $number,
                    'number' => 'INV-' . str_pad((string) $number, 4, '0', STR_PAD_LEFT),
                    'log' => $deletedLogs->get($number),
                ])->all(),
                'missing_count' => $missing->count(),
            ];
        }

        return $gaps;
    }

    private function authorizeSecurity(string $permission): void
    {
        if (!$this->security->allows($permission)) {
            abort(403, 'আপনার এই security পেজের অনুমতি (' . $permission . ') নেই।');
        }
    }
}
