<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\AuditLog;
use App\Models\CashClosing;
use App\Models\Cost;
use App\Models\Earn;
use App\Models\Invoice;
use App\Models\InvoiceCancelRequest;
use App\Models\InvoicePayment;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SecurityService
{
    public const TIMEZONE = 'Asia/Dhaka';

    public const APPROVER_ROLES = ['Super Admin', 'Owner', 'Owner - Hospital', 'Owner - Diagnostic'];

    public const DEFAULTS = [
        'security_owner_phones' => '',
        'security_alert_delete' => 'Yes',
        'security_alert_edit_reduce' => 'Yes',
        'security_alert_discount_percent' => '30',
        'security_alert_cancel_request' => 'Yes',
        'security_alert_cash_short' => 'Yes',
        'security_daily_summary' => 'Yes',
        'security_patient_bill_sms' => 'No',
        'security_payment_sms' => 'No',
        'security_invoice_lock_hours' => '24',
        'security_require_reason' => 'Yes',
        'security_cancel_approval' => 'Yes',
    ];

    public const SUMMARY_SENT_KEY = 'security_summary_last_date';

    public const PERMISSION_GROUP = 'security';

    public const PERMISSIONS = [
        'security.settings' => 'Security Settings দেখা/বদলানো ও test SMS',
        'security.report' => 'Suspicious Report দেখা',
        'security.logins' => 'Login History দেখা',
        'security.audit_logs' => 'Trash / Audit log দেখা',
        'security.audit_delete' => 'Trash / Audit রেকর্ড মুছে ফেলা',
        'security.cancel_approve' => 'Cancel request approve/reject এবং invoice সরাসরি delete',
        'security.invoice_override' => 'অন্যের invoice ও lock হওয়া invoice/cost edit/delete',
        'security.cash_closing_verify' => 'সব staff-এর cash closing দেখা ও verify',
        'security.dashboard' => 'Dashboard-এ security overview',
    ];

    /**
     * Never granted through the Owner fallback or on install; only roles an admin
     * explicitly picks in Roles get these.
     */
    public const EXPLICIT_ONLY_PERMISSIONS = ['security.audit_delete'];

    private static array $tablesCache = [];

    private static array $settingsCache = [];

    private static array $installedPermissionsCache = [];

    /**
     * Super Admin always passes. Until a permission row is installed on this database,
     * the old role rule (Owner / Super Admin) applies so nothing breaks before the install.
     */
    public function allows(string $permission, $admin = null): bool
    {
        $admin = $admin ?: auth('admin')->user();
        if (!$admin || !method_exists($admin, 'hasRole')) {
            return false;
        }
        if ($admin->hasRole('Super Admin')) {
            return true;
        }
        if (!in_array($permission, $this->installedPermissions(), true)) {
            return !in_array($permission, self::EXPLICIT_ONLY_PERMISSIONS, true)
                && $admin->hasAnyRole(self::APPROVER_ROLES);
        }

        return $admin->checkPermissionTo($permission, 'admin');
    }

    /**
     * @return string[]
     */
    public function installedPermissions(): array
    {
        $key = config('database.connections.mysql.database');

        return self::$installedPermissionsCache[$key] ??= \Spatie\Permission\Models\Permission::query()
            ->where('guard_name', 'admin')
            ->whereIn('name', array_keys(self::PERMISSIONS))
            ->pluck('name')
            ->all();
    }

    public function tablesReady(): bool
    {
        $key = config('database.connections.mysql.database');

        return self::$tablesCache[$key] ??= app(SecuritySchemaService::class)->isInstalled();
    }

    public static function flushCache(): void
    {
        self::$tablesCache = [];
        self::$settingsCache = [];
        self::$installedPermissionsCache = [];
    }

    public function setting(string $key, ?int $branchId = null): string
    {
        $branchId = $branchId ?? auth('admin')->user()?->branch_id;
        $default = self::DEFAULTS[$key] ?? '';

        if (!$branchId) {
            return (string) $default;
        }

        $memoKey = config('database.connections.mysql.database') . '.' . $branchId;
        $values = self::$settingsCache[$memoKey] ??= Setting::where('branch_id', $branchId)
            ->where('key', 'like', 'security\_%')
            ->pluck('value', 'key')
            ->all();
        $value = $values[$key] ?? null;

        return $value === null || $value === '' ? (string) $default : (string) $value;
    }

    public function enabled(string $key, ?int $branchId = null): bool
    {
        return $this->setting($key, $branchId) === 'Yes';
    }

    public function saveSettings(array $values, int $branchId): void
    {
        foreach (array_keys(self::DEFAULTS) as $key) {
            if (!array_key_exists($key, $values)) {
                continue;
            }
            Setting::updateOrCreate(
                ['branch_id' => $branchId, 'key' => $key],
                ['value' => (string) ($values[$key] ?? '')]
            );
        }
        self::$settingsCache = [];
    }

    /**
     * @return string[] 11-digit BD numbers
     */
    public function ownerPhones(int $branchId): array
    {
        $raw = preg_split('/[\s,;]+/', $this->setting('security_owner_phones', $branchId)) ?: [];

        return collect($raw)
            ->map(fn ($phone) => formatPhoneNumber($phone))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function reasonRequired(?int $branchId = null): bool
    {
        return $this->enabled('security_require_reason', $branchId);
    }

    public function reasonFrom(Request $request): ?string
    {
        $reason = trim((string) $request->input('audit_reason', ''));

        return $reason === '' ? null : mb_substr($reason, 0, 1000);
    }

    public function lockHours(?int $branchId = null): int
    {
        return max(0, (int) $this->setting('security_invoice_lock_hours', $branchId));
    }

    /**
     * Null when the current admin may still change the invoice.
     */
    public function invoiceLockMessage(Invoice $invoice, $admin = null): ?string
    {
        if ($this->allows('security.invoice_override', $admin)) {
            return null;
        }

        $hours = $this->lockHours($invoice->branch_id);
        if ($hours > 0 && $invoice->created_at && $invoice->created_at->lt(now()->subHours($hours))) {
            return "Invoice {$invoice->invoice_number} {$hours} ঘণ্টার বেশি পুরনো, তাই lock হয়ে গেছে। অনুমতি (invoice override) ছাড়া কেউ এটা পরিবর্তন করতে পারবে না।";
        }

        if ($invoice->creation_date && $this->cashClosedFor((int) $invoice->branch_id, (int) $invoice->admin_id, $invoice->creation_date)) {
            return "Invoice {$invoice->invoice_number}-এর দিনের cash closing হয়ে গেছে, তাই lock। অনুমতি (invoice override) ছাড়া কেউ এটা পরিবর্তন করতে পারবে না।";
        }

        return null;
    }

    public function ageLockMessage(\Illuminate\Database\Eloquent\Model $model, string $label, $admin = null): ?string
    {
        if ($this->allows('security.invoice_override', $admin)) {
            return null;
        }

        $hours = $this->lockHours($model->branch_id ?? null);
        if ($hours > 0 && $model->created_at && $model->created_at->lt(now()->subHours($hours))) {
            return "এই {$label} {$hours} ঘণ্টার বেশি পুরনো, তাই lock হয়ে গেছে। অনুমতি (invoice override) ছাড়া কেউ এটা পরিবর্তন করতে পারবে না।";
        }

        return null;
    }

    public function cashClosedFor(int $branchId, int $adminId, $date): bool
    {
        if (!$this->tablesReady() || !$adminId) {
            return false;
        }

        return CashClosing::where('branch_id', $branchId)
            ->where('admin_id', $adminId)
            ->whereDate('closing_date', Carbon::parse($date)->toDateString())
            ->exists();
    }

    public function discountPercent($netTotal, $discount): float
    {
        $gross = (float) $netTotal + (float) $discount;

        return $gross > 0 ? round(((float) $discount / $gross) * 100, 1) : 0.0;
    }

    public function paidAmount(Invoice $invoice): float
    {
        return (float) InvoicePayment::where('invoice_id', $invoice->id)->sum('paid_amount');
    }

    public function afterInvoiceCreated(Invoice $invoice): void
    {
        $branchId = (int) $invoice->branch_id;
        $percent = $this->discountPercent($invoice->total_amount, $invoice->discount_amount);
        $threshold = (float) $this->setting('security_alert_discount_percent', $branchId);

        if ($threshold > 0 && $percent >= $threshold) {
            $this->alertOwners($branchId, sprintf(
                'ALERT: %s discount %s%% (Tk %s of %s) by %s.',
                $invoice->invoice_number,
                $percent,
                $this->money($invoice->discount_amount),
                $this->money((float) $invoice->total_amount + (float) $invoice->discount_amount),
                $this->actorName()
            ));
        }

        if ($this->enabled('security_patient_bill_sms', $branchId)) {
            $paid = $this->paidAmount($invoice);
            $this->smsPatient($invoice, sprintf(
                '%s: Bill %s Tk %s, Paid %s, Due %s. -%s',
                $invoice->patient_name ? mb_substr($invoice->patient_name, 0, 20) : 'Patient',
                $invoice->invoice_number,
                $this->money($invoice->total_amount),
                $this->money($paid),
                $this->money(max(0, (float) $invoice->total_amount - $paid)),
                $this->companyName($branchId)
            ));
        }
    }

    public function afterInvoiceUpdated(array $old, Invoice $invoice, ?string $reason, bool $notifyPatient = true): void
    {
        $branchId = (int) $invoice->branch_id;
        $oldTotal = (float) ($old['total_amount'] ?? 0);
        $newTotal = (float) $invoice->total_amount;
        $oldDiscount = (float) ($old['discount_amount'] ?? 0);
        $newDiscount = (float) $invoice->discount_amount;
        $percent = $this->discountPercent($newTotal, $newDiscount);
        $threshold = (float) $this->setting('security_alert_discount_percent', $branchId);

        $problems = [];
        if ($this->enabled('security_alert_edit_reduce', $branchId) && $newTotal < $oldTotal) {
            $problems[] = 'bill Tk ' . $this->money($oldTotal) . '->' . $this->money($newTotal);
        }
        if ($threshold > 0 && $newDiscount > $oldDiscount && $percent >= $threshold) {
            $problems[] = 'discount ' . $percent . '%';
        }

        if ($problems) {
            $this->alertOwners($branchId, sprintf(
                'ALERT: %s edited (%s) by %s.%s',
                $invoice->invoice_number,
                implode(', ', $problems),
                $this->actorName(),
                $reason ? ' Reason: ' . mb_substr($reason, 0, 60) : ''
            ));
        }

        if ($notifyPatient && $newTotal !== $oldTotal && $this->enabled('security_patient_bill_sms', $branchId)) {
            $paid = $this->paidAmount($invoice);
            $this->smsPatient($invoice, sprintf(
                '%s updated: Bill Tk %s, Paid %s, Due %s. -%s',
                $invoice->invoice_number,
                $this->money($newTotal),
                $this->money($paid),
                $this->money(max(0, $newTotal - $paid)),
                $this->companyName($branchId)
            ));
        }
    }

    public function afterInvoiceDeleted(array $old, int $branchId, ?string $reason): void
    {
        if (!$this->enabled('security_alert_delete', $branchId)) {
            return;
        }

        $paid = collect($old['paid_amount'] ?? [])->sum('paid_amount');
        $this->alertOwners($branchId, sprintf(
            'ALERT: %s (Tk %s, paid %s) DELETED by %s.%s',
            $old['invoice_number'] ?? '#' . ($old['id'] ?? ''),
            $this->money($old['total_amount'] ?? 0),
            $this->money($paid),
            $this->actorName(),
            $reason ? ' Reason: ' . mb_substr($reason, 0, 60) : ''
        ));
    }

    public function afterCancelRequested(InvoiceCancelRequest $request): void
    {
        $branchId = (int) $request->branch_id;
        if (!$this->enabled('security_alert_cancel_request', $branchId)) {
            return;
        }

        $this->alertOwners($branchId, sprintf(
            'Cancel request: %s (Tk %s) by %s. Reason: %s. Approve/reject in software.',
            $request->invoice_number,
            $this->money($request->total_amount),
            $this->actorName(),
            mb_substr($request->reason, 0, 50)
        ));
    }

    public function afterDuePaid(Invoice $invoice, float $amount): void
    {
        $branchId = (int) $invoice->branch_id;
        if (!$this->enabled('security_payment_sms', $branchId)) {
            return;
        }

        $paid = $this->paidAmount($invoice);
        $this->smsPatient($invoice, sprintf(
            '%s: Tk %s %s. Total paid %s, Due %s. -%s',
            $invoice->invoice_number,
            $this->money(abs($amount)),
            $amount < 0 ? 'refunded' : 'received',
            $this->money($paid),
            $this->money(max(0, (float) $invoice->total_amount - $paid)),
            $this->companyName($branchId)
        ));
    }

    public function afterCashClosed(CashClosing $closing): void
    {
        $branchId = (int) $closing->branch_id;
        if ((float) $closing->difference >= 0 || !$this->enabled('security_alert_cash_short', $branchId)) {
            return;
        }

        $this->alertOwners($branchId, sprintf(
            'ALERT: Cash short Tk %s. %s closed %s: system %s, counted %s.',
            $this->money(abs((float) $closing->difference)),
            $closing->admin?->name ?? $this->actorName(),
            $closing->closing_date->format('d M'),
            $this->money($closing->system_amount),
            $this->money($closing->counted_amount)
        ));
    }

    /**
     * @return array<string, float|int>
     */
    public function dailyStats(int $branchId, string $date): array
    {
        $invoices = Invoice::where('branch_id', $branchId)->whereDate('creation_date', $date);
        [$from, $to] = $this->utcDayRange($date);

        $stats = [
            'invoice_count' => (clone $invoices)->count(),
            'invoice_total' => (float) (clone $invoices)->sum('total_amount'),
            'discount' => (float) (clone $invoices)->sum('discount_amount'),
            'collected' => (float) InvoicePayment::where('branch_id', $branchId)->whereDate('creation_date', $date)->sum('paid_amount'),
            'cost' => (float) Cost::where('branch_id', $branchId)->whereDate('creation_date', $date)->sum('amount'),
            'earn' => (float) Earn::where('branch_id', $branchId)->whereDate('date', $date)->sum('amount'),
            'edits' => 0,
            'deletes' => 0,
            'pending_cancel' => 0,
            'cash_short' => 0.0,
        ];

        if (Schema::hasTable('audit_logs')) {
            $logs = AuditLog::where('branch_id', $branchId)->where('module', 'invoice')->whereBetween('created_at', [$from, $to]);
            $stats['edits'] = (clone $logs)->where('action', 'updated')->count();
            $stats['deletes'] = (clone $logs)->where('action', 'deleted')->count();
        }

        if ($this->tablesReady()) {
            $stats['pending_cancel'] = InvoiceCancelRequest::where('branch_id', $branchId)
                ->where('status', InvoiceCancelRequest::STATUS_PENDING)->count();
            $stats['cash_short'] = abs((float) CashClosing::where('branch_id', $branchId)
                ->whereDate('closing_date', $date)->where('difference', '<', 0)->sum('difference'));
        }

        return $stats;
    }

    public function dailySummaryMessage(int $branchId, string $date): string
    {
        $s = $this->dailyStats($branchId, $date);

        $message = sprintf(
            '%s %s: Bills %d=Tk %s, Disc %s, Collected %s, Cost %s. Edit %d, Delete %d',
            $this->companyName($branchId),
            Carbon::parse($date)->format('d M'),
            $s['invoice_count'],
            $this->money($s['invoice_total']),
            $this->money($s['discount']),
            $this->money($s['collected']),
            $this->money($s['cost']),
            $s['edits'],
            $s['deletes']
        );

        if ($s['pending_cancel'] > 0) {
            $message .= ', Cancel req ' . $s['pending_cancel'];
        }
        if ($s['cash_short'] > 0) {
            $message .= ', Cash short ' . $this->money($s['cash_short']);
        }

        return $message . '.';
    }

    /**
     * Sends at most once per branch per day, whether triggered by the scheduler or a dashboard visit.
     */
    public function sendDailySummary(int $branchId, string $date): bool
    {
        if (!$this->enabled('security_daily_summary', $branchId) || empty($this->ownerPhones($branchId))) {
            return false;
        }

        if (Setting::getByBranch($branchId, self::SUMMARY_SENT_KEY) >= $date) {
            return false;
        }

        $lockKey = 'security-summary:' . config('database.connections.mysql.database') . ':' . $branchId . ':' . $date;
        if (!Cache::add($lockKey, 1, now()->addHours(6))) {
            return false;
        }

        Setting::updateOrCreate(
            ['branch_id' => $branchId, 'key' => self::SUMMARY_SENT_KEY],
            ['value' => $date]
        );

        $this->sendNow($branchId, $this->ownerPhones($branchId), $this->dailySummaryMessage($branchId, $date));

        return true;
    }

    public function sendPendingDailySummary(int $branchId): void
    {
        try {
            $yesterday = Carbon::now(self::TIMEZONE)->subDay()->toDateString();
            $this->sendDailySummary($branchId, $yesterday);
        } catch (\Throwable $e) {
            Log::warning('Daily security summary failed', ['branch_id' => $branchId, 'error' => $e->getMessage()]);
        }
    }

    public function alertOwners(int $branchId, string $message): void
    {
        $phones = $this->ownerPhones($branchId);
        if (empty($phones)) {
            return;
        }

        $this->afterResponse(fn () => $this->sendNow($branchId, $phones, $message));
    }

    public function smsPatient(Invoice $invoice, string $message): void
    {
        $phone = formatPhoneNumber($invoice->patient_phone);
        if (!$phone) {
            return;
        }

        $branchId = (int) $invoice->branch_id;
        $this->afterResponse(fn () => $this->sendNow($branchId, [$phone], $message));
    }

    public function companyName(int $branchId): string
    {
        return mb_substr((string) (Setting::getByBranch($branchId, 'company_name') ?: 'Hospital'), 0, 25);
    }

    public function money($value): string
    {
        $value = round((float) $value, 2);

        return $value == floor($value) ? number_format($value) : number_format($value, 2);
    }

    /**
     * @return array{0: Carbon, 1: Carbon} UTC bounds of a Dhaka calendar day
     */
    public function utcDayRange(string $date): array
    {
        $start = Carbon::parse($date, self::TIMEZONE)->startOfDay();

        return [$start->copy()->utc(), $start->copy()->endOfDay()->utc()];
    }

    private function actorName(): string
    {
        return mb_substr((string) (auth('admin')->user()?->name ?? 'system'), 0, 20);
    }

    private function sendNow(int $branchId, array $phones, string $message): void
    {
        foreach ($phones as $phone) {
            try {
                $result = smsSent($branchId, $phone, $message);
                if ($result !== 'SMS sent successfully.') {
                    Log::warning('Security SMS not sent', ['branch_id' => $branchId, 'phone' => $phone, 'result' => $result]);
                }
            } catch (\Throwable $e) {
                Log::warning('Security SMS failed', ['branch_id' => $branchId, 'phone' => $phone, 'error' => $e->getMessage()]);
            }
        }
    }

    /**
     * SMS gateways can be slow; web requests send after the response so staff are not kept waiting.
     */
    private function afterResponse(callable $callback): void
    {
        if (app()->runningInConsole()) {
            $callback();

            return;
        }

        app()->terminating($callback);
    }
}
