<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admit;
use App\Models\Cost;
use App\Models\DoctorSerial;
use App\Models\Earn;
use App\Models\Invoice;
use App\Models\InvoiceList;
use App\Models\InvoicePayment;
use App\Models\PharmacyProduct;
use App\Models\PharmacySale;
use App\Models\PharmacySalePayment;
use App\Models\ReceptPayment;
use App\Models\SmsBalance;
use App\Services\AuditLogSchemaService;
use App\Services\PatientInsightService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(AuditLogSchemaService $auditLogSchemaService)
    {
        $nowDhaka = Carbon::now('Asia/Dhaka');
        $branchId = auth()->user()->branch_id;
        $admin = auth('admin')->user();

        $data = [
            'auditLogSchemaStatus' => $auditLogSchemaService->getStatus(),
            'auditLogSchemaInstalled' => $auditLogSchemaService->isInstalled(),
            'canManageAuditLogs' => canAccessAuditLogs($admin),
            'todayLabel' => $nowDhaka->format('l, d M Y'),
            'adminName' => $admin?->name,
        ];

        if (!$admin || !$admin->can('dashboards.view')) {
            return view('backend.pages.dashboards.index', $data);
        }

        return view('backend.pages.dashboards.index', array_merge($data, $this->buildMetrics($branchId, $nowDhaka)));
    }

    public function liveStats(): JsonResponse
    {
        $admin = auth('admin')->user();

        if (!$admin || !$admin->can('dashboards.view')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $nowDhaka = Carbon::now('Asia/Dhaka');
        $branchId = $admin->branch_id;
        $cacheKey = 'dashboard_live_metrics_'.$branchId.'_'.intdiv($nowDhaka->timestamp, 20);

        $metrics = Cache::remember($cacheKey, 20, fn () => $this->buildMetrics($branchId, $nowDhaka));

        return response()->json($this->formatLivePayload($metrics, $nowDhaka));
    }

    private function formatLivePayload(array $metrics, Carbon $nowDhaka): array
    {
        return [
            'updated_at' => $nowDhaka->format('h:i:s A'),
            'updated_at_iso' => $nowDhaka->toIso8601String(),
            'patients' => $metrics['patients'],
            'today' => $metrics['today'],
            'yesterday' => $metrics['yesterday'],
            'thisWeek' => $metrics['thisWeek'],
            'thisMonth' => $metrics['thisMonth'],
            'lastWeek' => $metrics['lastWeek'],
            'lastMonth' => $metrics['lastMonth'],
            'lastYear' => $metrics['lastYear'],
            'comparisons' => $metrics['comparisons'],
            'operations' => $metrics['operations'],
            'pharmacy' => $metrics['pharmacy'],
            'opd' => $metrics['opd'],
            'alerts' => $this->buildLiveAlerts($metrics['operations'], $metrics['pharmacy']),
            'periods' => [
                $this->formatPeriodCard(t('common.this_week'), $metrics['thisWeek']),
                $this->formatPeriodCard(t('common.this_month'), $metrics['thisMonth']),
                $this->formatPeriodCard(t('common.last_week'), $metrics['lastWeek']),
                $this->formatPeriodCard(t('common.last_month'), $metrics['lastMonth']),
                $this->formatPeriodCard(t('common.last_year'), $metrics['lastYear']),
            ],
            'topTestsToday' => $metrics['topTestsToday']->map(fn ($row) => [
                'name' => $row->product?->name ?? t('dashboard.unknown_test'),
                'line_count' => (int) $row->line_count,
                'net_amount' => round((float) $row->net_amount, 2),
            ])->values(),
            'recentInvoices' => $metrics['recentInvoices']->map(fn ($inv) => [
                'id' => $inv->id,
                'invoice_number' => $inv->invoice_number,
                'patient_name' => $inv->patient_name,
                'creation_date' => $inv->creation_date
                    ? Carbon::parse($inv->creation_date)->format('d M Y')
                    : '—',
                'total_amount' => round((float) $inv->total_amount, 2),
                'paid' => round((float) ($inv->paid_amount_sum_paid_amount ?? 0), 2),
                'due' => round(max(0, (float) $inv->total_amount - (float) ($inv->paid_amount_sum_paid_amount ?? 0)), 2),
                'show_url' => route('admin.invoices.show', $inv->id),
            ])->values(),
            'activeAdmits' => $metrics['activeAdmits']->map(fn ($admit) => [
                'patient_name' => optional($admit->user)->name ?? '—',
                'doctor_name' => $admit->drreefer?->name,
                'admitted_at' => $admit->created_at ? $admit->created_at->format('d M Y') : '—',
                'manage_url' => route('admin.admits.release.details', $admit->id),
            ])->values(),
            'chart' => $metrics['chart'],
            'chartTodaySplit' => $metrics['chartTodaySplit'],
            'patientInsights' => $metrics['patientInsights'],
        ];
    }

    private function formatPeriodCard(string $label, array $data): array
    {
        return [
            'label' => $label,
            'from' => $data['from'],
            'to' => $data['to'],
            'net' => round((float) $data['net'], 2),
            'cost' => round((float) $data['cost'], 2),
            'collection' => [
                'invoice' => round((float) $data['collection']['invoice'], 2),
                'recept' => round((float) $data['collection']['recept'], 2),
                'earn' => round((float) $data['collection']['earn'], 2),
                'pharmacy' => round((float) ($data['collection']['pharmacy'] ?? 0), 2),
                'total' => round((float) $data['collection']['total'], 2),
            ],
        ];
    }

    private function buildLiveAlerts(array $operations, array $pharmacy): array
    {
        $fmt = fn ($n) => number_format((float) $n, 2);
        $alerts = [];

        if (($operations['outstanding_due'] ?? 0) > 0) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'fa-file-invoice-dollar',
                'icon_class' => 'text-danger',
                'title' => t('dashboard.patient_invoice_due'),
                'subtitle' => t('dashboard.unpaid_on_invoices', ['amount' => $fmt($operations['outstanding_due'])]),
                'url' => route('admin.invoices.index'),
            ];
        }

        if (($operations['refer_fee_due'] ?? 0) > 0) {
            $alerts[] = [
                'type' => 'warn',
                'icon' => 'fa-user-tie',
                'icon_class' => 'text-warning',
                'title' => t('dashboard.referrer_commission_due'),
                'subtitle' => t('dashboard.pending_payout', ['amount' => $fmt($operations['refer_fee_due'])]),
                'url' => route('admin.reports.references.payment'),
            ];
        }

        if (($operations['pending_lab_tests'] ?? 0) > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'fa-flask',
                'icon_class' => 'text-primary',
                'title' => t('dashboard.lab_tests_pending', ['count' => $operations['pending_lab_tests']]),
                'subtitle' => t('dashboard.completed_today', ['count' => $operations['completed_lab_today']]),
                'url' => route('admin.labs.index'),
            ];
        }

        if (($pharmacy['low_stock'] ?? 0) + ($pharmacy['out_of_stock'] ?? 0) > 0) {
            $alerts[] = [
                'type' => 'warn',
                'icon' => 'fa-pills',
                'icon_class' => 'text-warning',
                'title' => t('dashboard.pharmacy_stock_alert'),
                'subtitle' => t('dashboard.stock_out_low', [
                    'out' => $pharmacy['out_of_stock'],
                    'low' => $pharmacy['low_stock'],
                ]),
                'url' => route('admin.reports.pharmacy-stock'),
            ];
        }

        return $alerts;
    }

    private function buildMetrics(int $branchId, Carbon $nowDhaka): array
    {
        $todayStart = $nowDhaka->copy()->startOfDay();
        $todayEnd = $nowDhaka->copy()->endOfDay();

        $thisWeekStart = $nowDhaka->copy()->startOfWeek();
        $thisMonthStart = $nowDhaka->copy()->startOfMonth();

        $lastWeekStart = $nowDhaka->copy()->subWeek()->startOfWeek();
        $lastWeekEnd = $nowDhaka->copy()->subWeek()->endOfWeek();

        $lastMonthStart = $nowDhaka->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $nowDhaka->copy()->subMonth()->endOfMonth();

        $lastYearStart = $nowDhaka->copy()->subYear()->startOfYear();
        $lastYearEnd = $nowDhaka->copy()->subYear()->endOfYear();

        $yesterdayStart = $nowDhaka->copy()->subDay()->startOfDay();
        $yesterdayEnd = $nowDhaka->copy()->subDay()->endOfDay();

        $today = $this->periodSummary($branchId, $todayStart, $todayEnd);
        $yesterday = $this->periodSummary($branchId, $yesterdayStart, $yesterdayEnd);
        $thisWeek = $this->periodSummary($branchId, $thisWeekStart, $todayEnd);
        $thisMonth = $this->periodSummary($branchId, $thisMonthStart, $todayEnd);
        $lastWeek = $this->periodSummary($branchId, $lastWeekStart, $lastWeekEnd);
        $lastMonth = $this->periodSummary($branchId, $lastMonthStart, $lastMonthEnd);
        $lastYear = $this->periodSummary($branchId, $lastYearStart, $lastYearEnd);

        $operations = [
            'today_invoices' => Invoice::where('branch_id', $branchId)
                ->whereDate('creation_date', $nowDhaka->toDateString())
                ->count(),
            'today_invoice_amount' => (float) Invoice::where('branch_id', $branchId)
                ->whereDate('creation_date', $nowDhaka->toDateString())
                ->sum('total_amount'),
            'today_invoice_payments' => (int) InvoicePayment::where('branch_id', $branchId)
                ->whereDate('creation_date', $nowDhaka->toDateString())
                ->count(),
            'today_hospital_payments' => (int) ReceptPayment::where('branch_id', $branchId)
                ->whereDate('creation_date', $nowDhaka->toDateString())
                ->count(),
            'active_admits' => Admit::where('branch_id', $branchId)
                ->whereNull('release_at')
                ->count(),
            'pending_lab_tests' => InvoiceList::where('branch_id', $branchId)
                ->whereIn('status', [InvoiceList::$statusArray[0], InvoiceList::$statusArray[1]])
                ->count(),
            'completed_lab_today' => InvoiceList::where('branch_id', $branchId)
                ->where('status', InvoiceList::$statusArray[2])
                ->whereDate('updated_at', $nowDhaka->toDateString())
                ->count(),
            'outstanding_due' => $this->outstandingDue($branchId),
            'refer_fee_due' => $this->referFeeDue($branchId),
            'sms_balance' => optional(SmsBalance::where('branch_id', $branchId)->first())->balance ?? 0,
        ];

        $operations['payments_today'] = $operations['today_invoice_payments'] + $operations['today_hospital_payments'];

        $pharmacy = $this->pharmacySnapshot($branchId, $nowDhaka);
        $opd = $this->opdSnapshot($branchId, $nowDhaka);
        $patients = $this->patientActivitySnapshot($operations, $opd, $pharmacy);

        $topTestsToday = InvoiceList::query()
            ->where('branch_id', $branchId)
            ->whereDate('created_at', $nowDhaka->toDateString())
            ->select('product_id', DB::raw('COUNT(*) as line_count'), DB::raw('SUM(price - discount_price) as net_amount'))
            ->groupBy('product_id')
            ->orderByDesc('line_count')
            ->with('product:id,name')
            ->limit(5)
            ->get();

        $recentInvoices = Invoice::withSum('paidAmount', 'paid_amount')
            ->where('branch_id', $branchId)
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        $activeAdmits = Admit::with(['user', 'drreefer'])
            ->where('branch_id', $branchId)
            ->whereNull('release_at')
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        $chartLabels = [];
        $chartCollection = [];
        $chartCost = [];
        $chartNet = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = $nowDhaka->copy()->subDays($i);
            $summary = $this->periodSummary($branchId, $day->copy()->startOfDay(), $day->copy()->endOfDay());
            $chartLabels[] = $day->format('D');
            $chartCollection[] = round($summary['collection']['total'], 2);
            $chartCost[] = round($summary['cost'], 2);
            $chartNet[] = round($summary['net'], 2);
        }

        $chart = [
            'labels' => $chartLabels,
            'collection' => $chartCollection,
            'cost' => $chartCost,
            'net' => $chartNet,
        ];

        $chartTodaySplit = [
            'labels' => ['Diagnostic', 'Hospital', 'Pharmacy', 'Other Earn'],
            'values' => [
                round($today['collection']['invoice'], 2),
                round($today['collection']['recept'], 2),
                round($today['collection']['pharmacy'], 2),
                round($today['collection']['earn'], 2),
            ],
        ];

        $patientInsights = app(PatientInsightService::class)->snapshot($branchId, $nowDhaka);

        return [
            'today' => $today,
            'yesterday' => $yesterday,
            'thisWeek' => $thisWeek,
            'thisMonth' => $thisMonth,
            'lastWeek' => $lastWeek,
            'lastMonth' => $lastMonth,
            'lastYear' => $lastYear,
            'comparisons' => [
                'collection_vs_yesterday' => $this->percentChange(
                    $today['collection']['total'],
                    $yesterday['collection']['total']
                ),
                'net_vs_yesterday' => $this->percentChange($today['net'], $yesterday['net']),
                'net_week_vs_last_week' => $this->percentChange($thisWeek['net'], $lastWeek['net']),
                'net_month_vs_last_month' => $this->percentChange($thisMonth['net'], $lastMonth['net']),
            ],
            'todaysTotalCollection' => $today['collection']['total'],
            'todaysCost' => $today['cost'],
            'todaysInvoiceCollection' => $today['collection']['invoice'],
            'todaysReceptCollection' => $today['collection']['recept'],
            'todaysPharmacyCollection' => $today['collection']['pharmacy'],
            'todaysEarnCollection' => $today['collection']['earn'],
            'operations' => $operations,
            'pharmacy' => $pharmacy,
            'opd' => $opd,
            'patients' => $patients,
            'topTestsToday' => $topTestsToday,
            'recentInvoices' => $recentInvoices,
            'activeAdmits' => $activeAdmits,
            'chart' => $chart,
            'chartTodaySplit' => $chartTodaySplit,
            'patientInsights' => $patientInsights,
        ];
    }

    private function patientActivitySnapshot(array $operations, array $opd, array $pharmacy): array
    {
        $opdQueue = ($opd['pending'] ?? 0) + ($opd['checking'] ?? 0);
        $ipdActive = $operations['active_admits'] ?? 0;
        $labPending = $operations['pending_lab_tests'] ?? 0;

        return [
            'handling_now' => $opdQueue + $ipdActive + $labPending,
            'opd_queue' => $opdQueue,
            'opd_pending' => $opd['pending'] ?? 0,
            'opd_checking' => $opd['checking'] ?? 0,
            'ipd_active' => $ipdActive,
            'lab_pending' => $labPending,
            'today_footfall' => ($operations['today_invoices'] ?? 0)
                + ($opd['total_today'] ?? 0)
                + ($pharmacy['sales_today'] ?? 0),
            'today_invoices' => $operations['today_invoices'] ?? 0,
            'opd_total_today' => $opd['total_today'] ?? 0,
            'opd_completed_today' => $opd['completed'] ?? 0,
        ];
    }

    private function periodSummary(int $branchId, Carbon $start, Carbon $end): array
    {
        $collection = $this->collectionBetween($branchId, $start, $end);
        $cost = $this->costBetween($branchId, $start, $end);

        return [
            'collection' => $collection,
            'cost' => $cost,
            'net' => $collection['total'] - $cost,
            'from' => $start->toDateString(),
            'to' => $end->toDateString(),
        ];
    }

    private function collectionBetween(int $branchId, Carbon $start, Carbon $end): array
    {
        $invoice = (float) $this->whereDateColumnBetween(
            InvoicePayment::where('branch_id', $branchId),
            'creation_date',
            $start,
            $end
        )->sum('paid_amount');

        $recept = (float) $this->whereDateColumnBetween(
            ReceptPayment::where('branch_id', $branchId),
            'creation_date',
            $start,
            $end
        )->sum('paid_amount');

        $earn = (float) $this->whereDateColumnBetween(
            Earn::where('branch_id', $branchId),
            'date',
            $start,
            $end
        )->sum('amount');

        $pharmacy = $this->pharmacyCollectionBetween($branchId, $start, $end);

        return [
            'invoice' => $invoice,
            'recept' => $recept,
            'earn' => $earn,
            'pharmacy' => $pharmacy,
            'total' => $invoice + $recept + $earn + $pharmacy,
        ];
    }

    private function costBetween(int $branchId, Carbon $start, Carbon $end): float
    {
        return (float) $this->whereDateColumnBetween(
            Cost::where('branch_id', $branchId),
            'creation_date',
            $start,
            $end
        )->sum('amount');
    }

    /**
     * Filter Y-m-d string date columns (creation_date, sale_date, etc.).
     * Datetime whereBetween fails when the column stores date-only strings.
     */
    private function whereDateColumnBetween($query, string $column, Carbon $start, Carbon $end)
    {
        return $query
            ->where($column, '>=', $start->toDateString())
            ->where($column, '<=', $end->toDateString());
    }

    private function pharmacyCollectionBetween(int $branchId, Carbon $start, Carbon $end): float
    {
        $from = $start->toDateString();
        $to = $end->toDateString();

        $fromPayments = (float) $this->whereDateColumnBetween(
            PharmacySalePayment::where('branch_id', $branchId),
            'creation_date',
            $start,
            $end
        )->sum('paid_amount');

        $fromPosSales = (float) $this->whereDateColumnBetween(
            PharmacySale::where('branch_id', $branchId)->whereDoesntHave('payments'),
            'sale_date',
            $start,
            $end
        )->sum('paid_amount');

        return $fromPayments + $fromPosSales;
    }

    private function outstandingDue(int $branchId): float
    {
        return (float) DB::table('invoices as i')
            ->leftJoin(DB::raw('(
                SELECT invoice_id, SUM(paid_amount) AS paid
                FROM invoice_payments
                GROUP BY invoice_id
            ) AS p'), 'p.invoice_id', '=', 'i.id')
            ->where('i.branch_id', $branchId)
            ->whereRaw('i.total_amount > COALESCE(p.paid, 0)')
            ->selectRaw('SUM(i.total_amount - COALESCE(p.paid, 0)) AS due_sum')
            ->value('due_sum') ?? 0;
    }

    private function referFeeDue(int $branchId): float
    {
        return (float) DB::table('invoices as i')
            ->leftJoin(DB::raw('(
                SELECT invoice_id, SUM(amount) AS cost_sum
                FROM costs
                WHERE invoice_id IS NOT NULL
                GROUP BY invoice_id
            ) AS c'), 'c.invoice_id', '=', 'i.id')
            ->where('i.branch_id', $branchId)
            ->where('i.refer_fee_total', '>', 0)
            ->selectRaw('SUM(GREATEST(i.refer_fee_total - COALESCE(c.cost_sum, 0), 0)) AS due_sum')
            ->value('due_sum') ?? 0;
    }

    private function pharmacySnapshot(int $branchId, Carbon $nowDhaka): array
    {
        $todayQuery = PharmacySale::where('branch_id', $branchId)
            ->whereDate('sale_date', $nowDhaka->toDateString());

        $products = PharmacyProduct::query()->get(['id', 'alert_qty']);
        $stockMap = PharmacyProduct::stockMapForBranch($branchId);
        $lowStock = 0;
        $outOfStock = 0;

        foreach ($products as $product) {
            $stock = (float) ($stockMap[$product->id] ?? 0);
            if ($stock <= 0) {
                $outOfStock++;
            } elseif ($stock <= (float) $product->alert_qty) {
                $lowStock++;
            }
        }

        return [
            'sales_today' => (int) (clone $todayQuery)->count(),
            'collected_today' => (float) (clone $todayQuery)->sum('paid_amount'),
            'due_today' => (float) (clone $todayQuery)->sum('due_amount'),
            'low_stock' => $lowStock,
            'out_of_stock' => $outOfStock,
        ];
    }

    private function opdSnapshot(int $branchId, Carbon $nowDhaka): array
    {
        $base = DoctorSerial::where('branch_id', $branchId)
            ->whereDate('date', $nowDhaka->toDateString());

        return [
            'total_today' => (int) (clone $base)->count(),
            'pending' => (int) (clone $base)->where('status', DoctorSerial::$statusArray[0])->count(),
            'checking' => (int) (clone $base)->where('status', DoctorSerial::$statusArray[1])->count(),
            'completed' => (int) (clone $base)->where('status', DoctorSerial::$statusArray[2])->count(),
        ];
    }

    private function percentChange(float $current, float $previous): ?float
    {
        if ($previous == 0.0) {
            return $current > 0 ? 100.0 : null;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}
