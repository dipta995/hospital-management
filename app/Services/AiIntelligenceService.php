<?php

namespace App\Services;

use App\Http\Controllers\Backend\DashboardController;
use App\Models\Invoice;
use App\Models\InvoiceList;
use App\Models\ProductParameter;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AiIntelligenceService
{
    public function branchContext(int $branchId): array
    {
        $metrics = app(DashboardController::class)->buildMetricsForAi($branchId);
        $today = $metrics['today'] ?? [];
        $ops = $metrics['operations'] ?? [];
        $comparisons = $metrics['comparisons'] ?? [];
        $pharmacy = $metrics['pharmacy'] ?? [];
        $opd = $metrics['opd'] ?? [];
        $pi = $metrics['patientInsights'] ?? [];

        return [
            'date' => Carbon::now('Asia/Dhaka')->format('Y-m-d H:i'),
            'collection_today' => (float) ($today['collection']['total'] ?? 0),
            'cost_today' => (float) ($today['cost'] ?? 0),
            'net_today' => (float) ($today['net'] ?? 0),
            'collection_vs_yesterday' => $comparisons['collection_vs_yesterday'] ?? null,
            'net_vs_yesterday' => $comparisons['net_vs_yesterday'] ?? null,
            'outstanding_due' => (float) ($ops['outstanding_due'] ?? 0),
            'pending_labs' => (int) ($ops['pending_lab_tests'] ?? 0),
            'active_admits' => (int) ($ops['active_admits'] ?? 0),
            'refer_fee_due' => (float) ($ops['refer_fee_due'] ?? 0),
            'pharmacy_sales' => (int) ($pharmacy['sales_today'] ?? 0),
            'pharmacy_collected' => (float) ($pharmacy['collected_today'] ?? 0),
            'pharmacy_stock_out' => (int) ($pharmacy['stock_out'] ?? 0),
            'pharmacy_stock_low' => (int) ($pharmacy['stock_low'] ?? 0),
            'opd_pending' => (int) ($opd['pending'] ?? 0),
            'opd_total' => (int) ($opd['total_today'] ?? 0),
            'patients_today' => count($pi['today_patients'] ?? []),
            'at_risk_patients' => (int) ($pi['today_summary']['at_risk'] ?? 0),
            'chart_7d_net' => array_sum($metrics['chart']['net'] ?? []),
        ];
    }

    public function businessAnalysis(int $branchId): array
    {
        $ctx = $this->branchContext($branchId);
        $score = 100;
        $actions = [];
        $insights = [];
        $forecasts = [];

        $vsYesterday = $ctx['collection_vs_yesterday'];
        if ($vsYesterday !== null) {
            if ($vsYesterday < -15) {
                $score -= 15;
                $actions[] = $this->action('high', 'Revenue drop', 'Collection down '.abs(round($vsYesterday, 1)).'% vs yesterday. Review OPD flow and referrer activity.', 'admin.invoices.create');
                $insights[] = 'Revenue momentum is negative compared to yesterday.';
            } elseif ($vsYesterday > 10) {
                $insights[] = 'Strong collection growth (+'.round($vsYesterday, 1).'%) vs yesterday.';
            }
        }

        if ($ctx['outstanding_due'] > 50000) {
            $score -= 12;
            $actions[] = $this->action('high', 'High outstanding dues', '৳'.number_format($ctx['outstanding_due'], 0).' unpaid. Prioritize collection calls today.', 'admin.invoices.index');
        } elseif ($ctx['outstanding_due'] > 10000) {
            $score -= 5;
            $actions[] = $this->action('medium', 'Follow up dues', '৳'.number_format($ctx['outstanding_due'], 0).' outstanding on invoices.', 'admin.invoices.index');
        }

        if ($ctx['pending_labs'] > 20) {
            $score -= 10;
            $actions[] = $this->action('high', 'Lab backlog', $ctx['pending_labs'].' tests pending. Assign extra lab staff or extend hours.', 'admin.labs.index');
        } elseif ($ctx['pending_labs'] > 5) {
            $actions[] = $this->action('medium', 'Lab queue', $ctx['pending_labs'].' pending tests — monitor turnaround time.', 'admin.labs.index');
        }

        if ($ctx['pharmacy_stock_out'] > 0) {
            $score -= 8;
            $actions[] = $this->action('high', 'Pharmacy stock-out', $ctx['pharmacy_stock_out'].' items out of stock. Restock urgently.', 'admin.pharmacy_purchases.create');
        }

        if ($ctx['pharmacy_stock_low'] > 3) {
            $score -= 4;
            $actions[] = $this->action('medium', 'Low pharmacy stock', $ctx['pharmacy_stock_low'].' items below alert level.', 'admin.pharmacy_products.index');
        }

        if ($ctx['refer_fee_due'] > 20000) {
            $actions[] = $this->action('medium', 'Referrer payouts', '৳'.number_format($ctx['refer_fee_due'], 0).' commission due to referrers.', 'admin.reports.references.payment');
        }

        if ($ctx['at_risk_patients'] > 0) {
            $actions[] = $this->action('medium', 'At-risk patients', $ctx['at_risk_patients'].' at-risk patients active today. Schedule follow-up.', 'admin.users.index');
        }

        if ($ctx['net_today'] < 0) {
            $score -= 15;
            $insights[] = 'Today\'s net is negative — costs exceed collections.';
        } elseif ($ctx['net_today'] > 0) {
            $insights[] = 'Positive net today: ৳'.number_format($ctx['net_today'], 0).'.';
        }

        if ($ctx['opd_pending'] > 10) {
            $actions[] = $this->action('medium', 'OPD congestion', $ctx['opd_pending'].' patients waiting in OPD queue.', 'admin.doctor_serials.index');
        }

        $avgDailyNet = $ctx['chart_7d_net'] > 0 ? $ctx['chart_7d_net'] / 7 : $ctx['net_today'];
        $forecasts[] = [
            'label' => '7-day avg daily net',
            'value' => '৳'.number_format($avgDailyNet, 0),
            'trend' => $vsYesterday !== null && $vsYesterday > 0 ? 'up' : ($vsYesterday < 0 ? 'down' : 'flat'),
        ];

        if ($ctx['collection_today'] > 0 && $vsYesterday !== null && $vsYesterday > 0) {
            $projected = $ctx['collection_today'] * (1 + ($vsYesterday / 100) * 0.3);
            $forecasts[] = [
                'label' => 'Tomorrow collection (est.)',
                'value' => '৳'.number_format($projected, 0),
                'trend' => 'up',
            ];
        }

        $score = max(0, min(100, $score));
        $healthLabel = $score >= 80 ? 'excellent' : ($score >= 60 ? 'good' : ($score >= 40 ? 'fair' : 'critical'));

        usort($actions, fn ($a, $b) => ['high' => 0, 'medium' => 1, 'low' => 2][$a['severity']] <=> ['high' => 0, 'medium' => 1, 'low' => 2][$b['severity']]);

        return [
            'health_score' => $score,
            'health_label' => $healthLabel,
            'kpis' => [
                ['label' => 'Today net', 'value' => '৳'.number_format($ctx['net_today'], 0), 'icon' => 'fa-coins'],
                ['label' => 'Collection', 'value' => '৳'.number_format($ctx['collection_today'], 0), 'icon' => 'fa-hand-holding-usd'],
                ['label' => 'Pending labs', 'value' => (string) $ctx['pending_labs'], 'icon' => 'fa-flask'],
                ['label' => 'Outstanding', 'value' => '৳'.number_format($ctx['outstanding_due'], 0), 'icon' => 'fa-file-invoice-dollar'],
            ],
            'priority_actions' => array_slice($actions, 0, 6),
            'insights' => $insights,
            'forecasts' => $forecasts,
            'context' => $ctx,
        ];
    }

    public function analyzeLabLine(InvoiceList $line): array
    {
        $reportHtml = (string) $line->test_report;
        $plain = trim(strip_tags($reportHtml));
        $parameters = ProductParameter::where('product_id', $line->product_id)->get();
        $flags = [];

        foreach ($parameters as $param) {
            $name = $param->parameter;
            if (!$name || !$param->reference_range) {
                continue;
            }
            $pattern = '/'.preg_quote($name, '/').'[:\s]*([0-9]+\.?[0-9]*)/iu';
            if (preg_match($pattern, $plain, $m)) {
                $value = (float) $m[1];
                $range = $this->parseReferenceRange($param->reference_range);
                if ($range && ($value < $range['min'] || $value > $range['max'])) {
                    $flags[] = [
                        'parameter' => $name,
                        'value' => $value,
                        'unit' => $param->unit,
                        'reference' => $param->reference_range,
                        'status' => $value < $range['min'] ? 'low' : 'high',
                    ];
                }
            }
        }

        if (empty($flags) && $plain !== '') {
            if (preg_match_all('/\b(high|low|elevated|decreased|abnormal|positive|negative)\b/i', $plain, $matches)) {
                foreach (array_unique($matches[0]) as $word) {
                    $flags[] = [
                        'parameter' => 'Report text',
                        'value' => null,
                        'unit' => '',
                        'reference' => '—',
                        'status' => Str::lower($word),
                    ];
                }
            }
        }

        $interpretation = empty($flags)
            ? 'All reviewed parameters appear within expected ranges, or no structured parameters matched.'
            : count($flags).' parameter(s) flagged for clinical review.';

        return [
            'test_name' => $line->product?->name ?? 'Test',
            'flags' => $flags,
            'flag_count' => count($flags),
            'interpretation' => $interpretation,
            'follow_up' => count($flags) > 0
                ? 'Recommend physician review and patient callback if clinically significant.'
                : 'Routine delivery unless clinical context suggests otherwise.',
        ];
    }

    public function analyzePatient(array $profile, int $branchId): array
    {
        $stats = $profile['stats'] ?? [];
        $due = $profile['due']['total'] ?? 0;
        $segment = $profile['segment'] ?? 'new';
        $visits = (int) ($stats['visit_count'] ?? 0);
        $factors = [];
        $score = 25;

        if ($segment === 'at_risk') {
            $score += 35;
            $factors[] = ['label' => 'At-risk segment', 'impact' => 'high', 'detail' => 'Patient flagged as at-risk by visit pattern analysis.'];
        }
        if ($segment === 'special') {
            $score += 5;
            $factors[] = ['label' => 'VIP / Special', 'impact' => 'low', 'detail' => 'High-value patient — prioritize service quality.'];
        }
        if ($due > 5000) {
            $score += 15;
            $factors[] = ['label' => 'Outstanding balance', 'impact' => 'medium', 'detail' => '৳'.number_format((float) $due, 0).' due — may affect follow-up compliance.'];
        }
        if ($visits >= 10) {
            $score += 10;
            $factors[] = ['label' => 'Frequent visitor', 'impact' => 'medium', 'detail' => $visits.' lifetime visits — review chronic care needs.'];
        } elseif ($visits <= 1) {
            $factors[] = ['label' => 'New patient', 'impact' => 'low', 'detail' => 'Limited history — gather baseline labs and history.'];
        }

        if (!empty($profile['predictions'])) {
            foreach ($profile['predictions'] as $pred) {
                if (is_array($pred) && ($pred['tone'] ?? '') === 'warning') {
                    $score += 10;
                    $factors[] = ['label' => $pred['title'] ?? 'Alert', 'impact' => 'high', 'detail' => $pred['subtitle'] ?? ''];
                }
            }
        }

        $score = min(100, $score);
        $level = $score >= 70 ? 'high' : ($score >= 45 ? 'moderate' : 'low');

        $carePlan = [];
        if ($level === 'high') {
            $carePlan[] = 'Schedule follow-up within 7 days';
            $carePlan[] = 'Review recent lab results with treating physician';
        }
        if ($due > 0) {
            $carePlan[] = 'Address outstanding balance before next visit';
        }
        if ($segment === 'at_risk') {
            $carePlan[] = 'Outbound call — check adherence and satisfaction';
        }
        if (empty($carePlan)) {
            $carePlan[] = 'Continue standard monitoring and preventive care';
        }

        return [
            'risk_score' => $score,
            'risk_level' => $level,
            'factors' => $factors,
            'care_plan' => $carePlan,
        ];
    }

    public function contextPrompt(int $branchId): string
    {
        $ctx = $this->branchContext($branchId);
        $analysis = $this->businessAnalysis($branchId);

        return implode("\n", [
            'Live hospital snapshot ('.$ctx['date'].'):',
            '- Collection today: ৳'.number_format($ctx['collection_today'], 0),
            '- Net today: ৳'.number_format($ctx['net_today'], 0),
            '- Pending labs: '.$ctx['pending_labs'],
            '- Outstanding dues: ৳'.number_format($ctx['outstanding_due'], 0),
            '- Active admits: '.$ctx['active_admits'],
            '- OPD waiting: '.$ctx['opd_pending'],
            '- Business health score: '.$analysis['health_score'].'/100',
        ]);
    }

    public function chatSuggestions(int $branchId, string $locale = 'en'): array
    {
        $ctx = $this->branchContext($branchId);
        $bn = $locale === 'bn';

        $suggestions = [
            $bn ? 'আজকের আদায় ও নিট লাভ কেমন?' : 'How is today\'s collection and net profit?',
            $bn ? 'পেন্ডিং ল্যাব টেস্ট কীভাবে কমাব?' : 'How to reduce pending lab tests?',
            $bn ? 'ফার্মেসি স্টক লো হলে কী করব?' : 'What to do when pharmacy stock is low?',
            $bn ? 'রোগীর Patient 360 কোথায়?' : 'Where is Patient 360 profile?',
        ];

        if ($ctx['pending_labs'] > 5) {
            $suggestions[] = $bn
                ? $ctx['pending_labs'].'টি পেন্ডিং ল্যাব — আজ কী অগ্রাধিকার দেব?'
                : $ctx['pending_labs'].' pending labs — what should I prioritize today?';
        }
        if ($ctx['outstanding_due'] > 10000) {
            $suggestions[] = $bn ? 'বকেয়া ইনভয়েস কালেকশন টিপস দাও' : 'Tips for collecting outstanding invoice dues';
        }

        return array_slice($suggestions, 0, 6);
    }

    private function action(string $severity, string $title, string $detail, string $route): array
    {
        return [
            'severity' => $severity,
            'title' => $title,
            'detail' => $detail,
            'route' => $route,
        ];
    }

    private function parseReferenceRange(string $range): ?array
    {
        $range = trim($range);
        if (preg_match('/^([\d.]+)\s*[-–]\s*([\d.]+)$/', $range, $m)) {
            return ['min' => (float) $m[1], 'max' => (float) $m[2]];
        }
        if (preg_match('/^[<≤]\s*([\d.]+)$/', $range, $m)) {
            return ['min' => 0, 'max' => (float) $m[1]];
        }
        if (preg_match('/^[>≥]\s*([\d.]+)$/', $range, $m)) {
            return ['min' => (float) $m[1], 'max' => PHP_FLOAT_MAX];
        }

        return null;
    }
}
