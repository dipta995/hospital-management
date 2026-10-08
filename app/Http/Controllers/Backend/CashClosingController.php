<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\CashClosing;
use App\Models\InvoicePayment;
use App\Services\SecurityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashClosingController extends Controller
{
    public function __construct(private SecurityService $security)
    {
        $this->checkGuard();
        \Illuminate\Pagination\Paginator::useBootstrapFive();
    }

    public function index(Request $request)
    {
        $this->checkOwnPermission('invoices.index');
        $branchId = (int) auth()->user()->branch_id;
        $canApprove = $this->security->allows('security.cash_closing_verify');
        $tableReady = $this->security->tablesReady();
        $today = Carbon::now(SecurityService::TIMEZONE)->toDateString();
        $date = $canApprove && $request->filled('date') ? Carbon::parse($request->date)->toDateString() : $today;

        $myCollection = $this->collection($branchId, (int) auth()->id(), $today);
        $myClosing = $tableReady
            ? CashClosing::where('branch_id', $branchId)->where('admin_id', auth()->id())->whereDate('closing_date', $today)->first()
            : null;

        $staffRows = collect();
        if ($canApprove && $tableReady) {
            $collectorIds = InvoicePayment::where('branch_id', $branchId)->whereDate('creation_date', $date)
                ->distinct()->pluck('admin_id');
            $closings = CashClosing::with(['admin', 'verifier'])->where('branch_id', $branchId)
                ->whereDate('closing_date', $date)->get()->keyBy('admin_id');
            $admins = Admin::whereIn('id', $collectorIds->merge($closings->keys())->unique())->get()->keyBy('id');

            $staffRows = $admins->map(fn ($admin) => [
                'admin' => $admin,
                'live' => $this->collection($branchId, (int) $admin->id, $date),
                'closing' => $closings->get($admin->id),
            ])->sortByDesc(fn ($row) => $row['live']['amount'])->values();
        }

        $history = $tableReady
            ? CashClosing::with(['admin', 'verifier'])
                ->where('branch_id', $branchId)
                ->when(!$canApprove, fn ($q) => $q->where('admin_id', auth()->id()))
                ->orderByDesc('closing_date')->orderByDesc('id')
                ->paginate(20)->withQueryString()
            : collect();

        return view('backend.pages.security.cash-closings', compact(
            'canApprove', 'tableReady', 'today', 'date', 'myCollection', 'myClosing', 'staffRows', 'history'
        ));
    }

    public function store(Request $request)
    {
        $this->checkOwnPermission('invoices.index');
        if (!$this->security->tablesReady()) {
            return RedirectHelper::backWithInputFromException('<strong>Sorry!!!</strong> Security tables এখনো install করা হয়নি।');
        }

        $request->validate([
            'counted_amount' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:1000',
        ]);

        $branchId = (int) auth()->user()->branch_id;
        $today = Carbon::now(SecurityService::TIMEZONE)->toDateString();
        $difference = round((float) $request->counted_amount, 2);

        try {
            $closing = DB::transaction(function () use ($request, $branchId, $today, &$difference) {
                if (CashClosing::where('branch_id', $branchId)->where('admin_id', auth()->id())->whereDate('closing_date', $today)->lockForUpdate()->exists()) {
                    return null;
                }

                $collection = $this->collection($branchId, (int) auth()->id(), $today);
                $difference = round((float) $request->counted_amount - $collection['amount'], 2);

                return CashClosing::create([
                    'branch_id' => $branchId,
                    'admin_id' => auth()->id(),
                    'closing_date' => $today,
                    'payment_count' => $collection['count'],
                    'system_amount' => $collection['amount'],
                    'counted_amount' => $request->counted_amount,
                    'difference' => $difference,
                    'breakdown' => $collection['breakdown'],
                    'note' => $request->note,
                    'status' => CashClosing::STATUS_CLOSED,
                ]);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            $closing = null;
        }

        if (!$closing) {
            return RedirectHelper::backWithInputFromException('<strong>Sorry!!!</strong> আজকের cash closing আগেই করা হয়ে গেছে।');
        }

        $this->security->afterCashClosed($closing->load('admin'));

        $message = $difference < 0
            ? '<strong>Closed.</strong> ৳' . $this->security->money(abs($difference)) . ' কম আছে — Owner-কে জানানো হয়েছে।'
            : '<strong>Closed!</strong> আজকের cash closing সংরক্ষণ হয়েছে। এই দিনের invoice এখন lock।';

        return RedirectHelper::back($message);
    }

    public function verify(Request $request, CashClosing $cashClosing)
    {
        if (!$this->security->allows('security.cash_closing_verify')) {
            abort(403, 'Cash closing verify করার অনুমতি (security.cash_closing_verify) নেই।');
        }
        if ((int) $cashClosing->branch_id !== (int) auth()->user()->branch_id) {
            abort(404);
        }

        $cashClosing->update([
            'status' => CashClosing::STATUS_VERIFIED,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'verify_note' => trim((string) $request->input('verify_note', '')) ?: null,
        ]);

        return RedirectHelper::back('<strong>Verified!</strong> Cash বুঝে পাওয়া হিসেবে চিহ্নিত হয়েছে।');
    }

    /**
     * @return array{count:int, amount:float, breakdown:array<string,float>}
     */
    private function collection(int $branchId, int $adminId, string $date): array
    {
        $rows = InvoicePayment::query()
            ->leftJoin('invoices', 'invoices.id', '=', 'invoice_payments.invoice_id')
            ->where('invoice_payments.branch_id', $branchId)
            ->where('invoice_payments.admin_id', $adminId)
            ->whereDate('invoice_payments.creation_date', $date)
            ->groupBy('invoices.payment_type')
            ->selectRaw("COALESCE(invoices.payment_type, 'Cash') as method, COUNT(*) as payments, SUM(invoice_payments.paid_amount) as total")
            ->get();

        return [
            'count' => (int) $rows->sum('payments'),
            'amount' => round((float) $rows->sum('total'), 2),
            'breakdown' => $rows->mapWithKeys(fn ($row) => [$row->method ?: 'Cash' => round((float) $row->total, 2)])->all(),
        ];
    }
}
