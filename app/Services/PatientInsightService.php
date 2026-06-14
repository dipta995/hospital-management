<?php

namespace App\Services;

use App\Models\Admit;
use App\Models\DoctorSerial;
use App\Models\Invoice;
use App\Models\PharmacySale;
use App\Models\Recept;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PatientInsightService
{
    public const SEGMENT_SPECIAL = 'special';
    public const SEGMENT_REGULAR = 'regular';
    public const SEGMENT_NEW = 'new';
    public const SEGMENT_RETURNING = 'returning';
    public const SEGMENT_OCCASIONAL = 'occasional';
    public const SEGMENT_AT_RISK = 'at_risk';

    public static function segmentLabels(): array
    {
        return [
            self::SEGMENT_SPECIAL => 'Special / VIP',
            self::SEGMENT_REGULAR => 'Regular',
            self::SEGMENT_NEW => 'New',
            self::SEGMENT_RETURNING => 'Returning',
            self::SEGMENT_OCCASIONAL => 'Occasional',
            self::SEGMENT_AT_RISK => 'At Risk',
        ];
    }

    public function snapshot(int $branchId, Carbon $now): array
    {
        $cacheKey = 'dashboard_patient_insights_'.$branchId.'_'.$now->format('Y-m-d-H').intval($now->minute / 2);

        return Cache::remember($cacheKey, 120, function () use ($branchId, $now) {
            return $this->buildSnapshot($branchId, $now);
        });
    }

    private function buildSnapshot(int $branchId, Carbon $now): array
    {
        $profiles = $this->buildProfiles($branchId, $now);
        $todayKey = $now->toDateString();

        $summary = array_fill_keys(array_keys(self::segmentLabels()), 0);
        $todaySummary = array_fill_keys(array_keys(self::segmentLabels()), 0);
        $todayPatients = [];
        $predictions = [];
        $likelyReturn = 0;
        $winBack = 0;

        foreach ($profiles as $profile) {
            $segment = $this->classify($profile, $now);
            $summary[$segment] = ($summary[$segment] ?? 0) + 1;

            if ($profile['last_visit'] && $profile['last_visit']->toDateString() === $todayKey) {
                $todaySummary[$segment] = ($todaySummary[$segment] ?? 0) + 1;
                $todayPatients[] = $this->formatPatientRow($profile, $segment, $now, true);
            }

            if ($segment === self::SEGMENT_AT_RISK) {
                $winBack++;
            }

            if ($this->isLikelyToReturnSoon($profile, $now)) {
                $likelyReturn++;
            }
        }

        usort($todayPatients, fn ($a, $b) => $this->segmentPriority($a['segment']) <=> $this->segmentPriority($b['segment']));

        if ($likelyReturn > 0) {
            $predictions[] = [
                'type' => 'likely_return',
                'icon' => 'fa-calendar-check',
                'tone' => 'info',
                'count' => $likelyReturn,
                'title' => $likelyReturn.' regular patient(s) may visit soon',
                'subtitle' => 'Based on their usual visit interval',
            ];
        }

        if ($winBack > 0) {
            $predictions[] = [
                'type' => 'win_back',
                'icon' => 'fa-sms',
                'tone' => 'warn',
                'count' => $winBack,
                'title' => $winBack.' at-risk patient(s) need follow-up',
                'subtitle' => 'No visit in 45–120 days but were regular before',
            ];
        }

        $specialToday = $todaySummary[self::SEGMENT_SPECIAL] ?? 0;
        if ($specialToday > 0) {
            $predictions[] = [
                'type' => 'special_today',
                'icon' => 'fa-star',
                'tone' => 'special',
                'count' => $specialToday,
                'title' => $specialToday.' special/VIP patient(s) today',
                'subtitle' => 'Prioritize service — high value or frequent visitor',
            ];
        }

        $newToday = $todaySummary[self::SEGMENT_NEW] ?? 0;
        if ($newToday > 0) {
            $predictions[] = [
                'type' => 'new_today',
                'icon' => 'fa-user-plus',
                'tone' => 'success',
                'count' => $newToday,
                'title' => $newToday.' new patient(s) today',
                'subtitle' => 'Good chance to convert into regular patients',
            ];
        }

        $topPatients = collect($profiles)
            ->sortByDesc('total_spent')
            ->take(8)
            ->map(function ($profile) use ($now) {
                $segment = $this->classify($profile, $now);

                return $this->formatPatientRow($profile, $segment, $now, false);
            })
            ->values()
            ->all();

        return [
            'summary' => $summary,
            'today_summary' => $todaySummary,
            'today_total' => array_sum($todaySummary),
            'active_patients' => count($profiles),
            'today_patients' => array_slice($todayPatients, 0, 12),
            'top_patients' => $topPatients,
            'predictions' => $predictions,
            'chart_segments' => [
                'labels' => array_values(self::segmentLabels()),
                'values' => array_values($summary),
                'keys' => array_keys(self::segmentLabels()),
            ],
            'chart_today_segments' => [
                'labels' => array_values(self::segmentLabels()),
                'values' => array_values($todaySummary),
                'keys' => array_keys(self::segmentLabels()),
            ],
        ];
    }

    private function buildProfiles(int $branchId, Carbon $now): array
    {
        $profiles = [];
        $cutoff = $now->copy()->subDays(365);

        $this->mergeTouchpoints($profiles, Invoice::query()
            ->where('branch_id', $branchId)
            ->where('creation_date', '>=', $cutoff)
            ->get(['patient_phone', 'patient_name', 'creation_date', 'total_amount'])
            ->map(fn ($row) => [
                'phone' => $row->patient_phone,
                'name' => $row->patient_name,
                'date' => $row->creation_date ? Carbon::parse($row->creation_date) : null,
                'amount' => (float) $row->total_amount,
            ]));

        $this->mergeTouchpoints($profiles, DoctorSerial::query()
            ->where('branch_id', $branchId)
            ->where('date', '>=', $cutoff->toDateString())
            ->get(['patient_phone', 'patient_name', 'date', 'amount'])
            ->map(fn ($row) => [
                'phone' => $row->patient_phone,
                'name' => $row->patient_name,
                'date' => $row->date ? Carbon::parse($row->date) : null,
                'amount' => (float) $row->amount,
            ]));

        $this->mergeTouchpoints($profiles, Recept::query()
            ->where('branch_id', $branchId)
            ->where('created_date', '>=', $cutoff->toDateString())
            ->with('user:id,name,phone')
            ->get(['id', 'user_id', 'created_date', 'total_amount'])
            ->map(fn ($row) => [
                'phone' => $row->user?->phone,
                'name' => $row->user?->name,
                'user_id' => $row->user_id,
                'date' => $row->created_date ? Carbon::parse($row->created_date) : null,
                'amount' => (float) $row->total_amount,
            ]));

        $this->mergeTouchpoints($profiles, PharmacySale::query()
            ->where('branch_id', $branchId)
            ->where('sale_date', '>=', $cutoff->toDateString())
            ->with('customer:id,name,phone')
            ->get(['id', 'customer_id', 'sale_date', 'total_amount'])
            ->map(fn ($row) => [
                'phone' => $row->customer?->phone,
                'name' => $row->customer?->name,
                'user_id' => $row->customer_id,
                'date' => $row->sale_date ? Carbon::parse($row->sale_date) : null,
                'amount' => (float) $row->total_amount,
            ]));

        $this->mergeTouchpoints($profiles, Admit::query()
            ->where('branch_id', $branchId)
            ->where('created_at', '>=', $cutoff)
            ->with('user:id,name,phone')
            ->get(['id', 'user_id', 'created_at', 'release_at'])
            ->map(fn ($row) => [
                'phone' => $row->user?->phone,
                'name' => $row->user?->name,
                'user_id' => $row->user_id,
                'date' => $row->created_at ? Carbon::parse($row->created_at) : null,
                'amount' => 0.0,
                'is_admit' => true,
                'active_admit' => $row->release_at === null,
            ]));

        $this->attachDueFlags($profiles, $branchId);
        $this->attachUserIds($profiles);

        return array_values($profiles);
    }

    private function mergeTouchpoints(array &$profiles, Collection $rows): void
    {
        foreach ($rows as $row) {
            $key = $this->patientKey($row['phone'] ?? null, $row['user_id'] ?? null, $row['name'] ?? null);
            if (!$key) {
                continue;
            }

            if (!isset($profiles[$key])) {
                $profiles[$key] = [
                    'key' => $key,
                    'name' => trim((string) ($row['name'] ?? 'Unknown')),
                    'phone' => $this->displayPhone($row['phone'] ?? null),
                    'user_id' => $row['user_id'] ?? null,
                    'visit_count' => 0,
                    'visits_90d' => 0,
                    'total_spent' => 0.0,
                    'first_visit' => null,
                    'last_visit' => null,
                    'visit_dates' => [],
                    'has_due' => false,
                    'active_admit' => false,
                    'returned_after_gap' => false,
                ];
            }

            $profile = &$profiles[$key];

            if (!empty($row['name']) && ($profile['name'] === 'Unknown' || strlen($row['name']) > strlen($profile['name']))) {
                $profile['name'] = trim($row['name']);
            }

            if (!empty($row['user_id'])) {
                $profile['user_id'] = $row['user_id'];
            }

            if (!empty($row['active_admit'])) {
                $profile['active_admit'] = true;
            }

            if (empty($row['date'])) {
                continue;
            }

            /** @var Carbon $date */
            $date = $row['date'];
            $profile['visit_count']++;
            $profile['total_spent'] += (float) ($row['amount'] ?? 0);
            $profile['visit_dates'][] = $date->copy();

            if (!$profile['first_visit'] || $date->lt($profile['first_visit'])) {
                $profile['first_visit'] = $date->copy();
            }

            if (!$profile['last_visit'] || $date->gt($profile['last_visit'])) {
                $profile['last_visit'] = $date->copy();
            }
        }
    }

    private function attachDueFlags(array &$profiles, int $branchId): void
    {
        $dues = DB::table('invoices as i')
            ->leftJoin(DB::raw('(
                SELECT invoice_id, SUM(paid_amount) AS paid
                FROM invoice_payments
                GROUP BY invoice_id
            ) AS p'), 'p.invoice_id', '=', 'i.id')
            ->where('i.branch_id', $branchId)
            ->whereRaw('i.total_amount > COALESCE(p.paid, 0)')
            ->get(['i.patient_phone', 'i.patient_name']);

        foreach ($dues as $due) {
            $key = $this->patientKey($due->patient_phone, null, $due->patient_name);
            if ($key && isset($profiles[$key])) {
                $profiles[$key]['has_due'] = true;
            }
        }
    }

    private function attachUserIds(array &$profiles): void
    {
        $phones = collect($profiles)
            ->pluck('phone')
            ->filter()
            ->map(fn ($p) => $this->normalizePhone($p))
            ->unique()
            ->values();

        if ($phones->isEmpty()) {
            return;
        }

        $users = User::query()
            ->whereNotNull('phone')
            ->get(['id', 'phone']);

        $phoneToUser = [];
        foreach ($users as $user) {
            $normalized = $this->normalizePhone($user->phone);
            if ($normalized) {
                $phoneToUser[$normalized] = $user->id;
            }
        }

        foreach ($profiles as &$profile) {
            if ($profile['user_id']) {
                continue;
            }
            $normalized = $this->normalizePhone($profile['phone']);
            if ($normalized && isset($phoneToUser[$normalized])) {
                $profile['user_id'] = $phoneToUser[$normalized];
            }
        }
    }

    private function classify(array $profile, Carbon $now): string
    {
        $visitDates = collect($profile['visit_dates'] ?? [])->sort()->values();
        $visitCount = (int) $profile['visit_count'];
        $visits90d = $visitDates->filter(fn (Carbon $d) => $d->gte($now->copy()->subDays(90)))->count();
        $totalSpent = (float) $profile['total_spent'];
        $lastVisit = $profile['last_visit'];
        $daysSinceLast = $lastVisit ? $lastVisit->diffInDays($now) : 999;

        if ($profile['active_admit'] || $totalSpent >= 25000 || $visitCount >= 8) {
            return self::SEGMENT_SPECIAL;
        }

        if ($visitCount >= 3 && $daysSinceLast >= 45 && $daysSinceLast <= 120) {
            return self::SEGMENT_AT_RISK;
        }

        if ($visitCount === 1 && $profile['first_visit'] && $profile['first_visit']->gte($now->copy()->subDays(30))) {
            return self::SEGMENT_NEW;
        }

        if ($this->hasReturnGap($visitDates, 30)) {
            return self::SEGMENT_RETURNING;
        }

        if ($visits90d >= 3 || $visitCount >= 5) {
            return self::SEGMENT_REGULAR;
        }

        return self::SEGMENT_OCCASIONAL;
    }

    private function isLikelyToReturnSoon(array $profile, Carbon $now): bool
    {
        if (($profile['visit_count'] ?? 0) < 3 || empty($profile['last_visit'])) {
            return false;
        }

        $dates = collect($profile['visit_dates'] ?? [])->sort()->values();
        if ($dates->count() < 2) {
            return false;
        }

        $gaps = [];
        for ($i = 1; $i < $dates->count(); $i++) {
            $gaps[] = $dates[$i - 1]->diffInDays($dates[$i]);
        }

        $avgGap = array_sum($gaps) / max(count($gaps), 1);
        $daysSince = $profile['last_visit']->diffInDays($now);

        return $daysSince >= max(7, (int) floor($avgGap * 0.85))
            && $daysSince <= (int) ceil($avgGap * 1.4)
            && $daysSince < 45;
    }

    private function hasReturnGap(Collection $dates, int $minGapDays): bool
    {
        if ($dates->count() < 2) {
            return false;
        }

        for ($i = 1; $i < $dates->count(); $i++) {
            if ($dates[$i - 1]->diffInDays($dates[$i]) >= $minGapDays) {
                return true;
            }
        }

        return false;
    }

    private function formatPatientRow(array $profile, string $segment, Carbon $now, bool $todayContext): array
    {
        return [
            'name' => $profile['name'],
            'phone' => $profile['phone'] ?: '—',
            'segment' => $segment,
            'segment_label' => self::segmentLabels()[$segment] ?? ucfirst($segment),
            'visit_count' => (int) $profile['visit_count'],
            'total_spent' => round((float) $profile['total_spent'], 2),
            'last_visit' => $profile['last_visit'] ? $profile['last_visit']->format('d M Y') : '—',
            'has_due' => (bool) ($profile['has_due'] ?? false),
            'active_admit' => (bool) ($profile['active_admit'] ?? false),
            'prediction' => $this->predictionHint($profile, $segment, $now, $todayContext),
        ];
    }

    private function predictionHint(array $profile, string $segment, Carbon $now, bool $todayContext): string
    {
        if ($todayContext && $segment === self::SEGMENT_SPECIAL) {
            return 'VIP today — give priority service';
        }

        if ($todayContext && $segment === self::SEGMENT_NEW) {
            return 'First-time — explain services, build trust';
        }

        if ($segment === self::SEGMENT_AT_RISK) {
            return 'May not return — call or SMS reminder';
        }

        if ($this->isLikelyToReturnSoon($profile, $now)) {
            return 'Likely to visit this week';
        }

        if ($segment === self::SEGMENT_REGULAR) {
            return 'Stable regular — maintain relationship';
        }

        if ($profile['has_due'] ?? false) {
            return 'Has due — follow up payment';
        }

        return 'Monitor visit pattern';
    }

    private function segmentPriority(string $segment): int
    {
        return match ($segment) {
            self::SEGMENT_SPECIAL => 1,
            self::SEGMENT_NEW => 2,
            self::SEGMENT_AT_RISK => 3,
            self::SEGMENT_RETURNING => 4,
            self::SEGMENT_REGULAR => 5,
            default => 6,
        };
    }

    private function patientKey(?string $phone, ?int $userId, ?string $name): ?string
    {
        $normalizedPhone = $this->normalizePhone($phone);
        if ($normalizedPhone) {
            return 'phone:'.$normalizedPhone;
        }

        if ($userId) {
            return 'user:'.$userId;
        }

        $cleanName = strtolower(trim(preg_replace('/\s+/', ' ', (string) $name)));
        if ($cleanName !== '' && $cleanName !== 'unknown') {
            return 'name:'.$cleanName;
        }

        return null;
    }

    private function normalizePhone(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $phone);
        if (strlen($digits) < 6) {
            return null;
        }

        return strlen($digits) > 11 ? substr($digits, -11) : $digits;
    }

    private function displayPhone(?string $phone): ?string
    {
        $normalized = $this->normalizePhone($phone);

        return $normalized ?: ($phone ? trim($phone) : null);
    }

    public function search(int $branchId, string $query, int $limit = 8): array
    {
        $query = trim($query);
        if (mb_strlen($query) < 2) {
            return [];
        }

        $results = [];
        $seen = [];

        $push = function (?string $phone, ?string $name, ?int $userId = null, ?string $subtitle = null) use (&$results, &$seen, $limit) {
            if (count($results) >= $limit) {
                return;
            }

            $key = $this->patientKey($phone, $userId, $name);
            if (!$key || isset($seen[$key])) {
                return;
            }

            $seen[$key] = true;
            $results[] = [
                'key' => $key,
                'name' => trim($name ?: 'Unknown'),
                'phone' => $this->displayPhone($phone) ?: '—',
                'user_id' => $userId,
                'subtitle' => $subtitle,
            ];
        };

        User::query()
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', '%'.$query.'%')
                    ->orWhere('phone', 'LIKE', '%'.$query.'%');
            })
            ->orderByDesc('id')
            ->limit($limit)
            ->get(['id', 'name', 'phone'])
            ->each(fn ($user) => $push($user->phone, $user->name, $user->id, 'Registered patient'));

        Invoice::query()
            ->where('branch_id', $branchId)
            ->where(function ($q) use ($query) {
                $q->where('patient_name', 'LIKE', '%'.$query.'%')
                    ->orWhere('patient_phone', 'LIKE', '%'.$query.'%');
            })
            ->orderByDesc('id')
            ->limit($limit)
            ->get(['patient_name', 'patient_phone'])
            ->each(fn ($row) => $push($row->patient_phone, $row->patient_name, null, 'Diagnostic invoice'));

        DoctorSerial::query()
            ->where('branch_id', $branchId)
            ->where(function ($q) use ($query) {
                $q->where('patient_name', 'LIKE', '%'.$query.'%')
                    ->orWhere('patient_phone', 'LIKE', '%'.$query.'%');
            })
            ->orderByDesc('id')
            ->limit($limit)
            ->get(['patient_name', 'patient_phone'])
            ->each(fn ($row) => $push($row->patient_phone, $row->patient_name, null, 'OPD serial'));

        return array_slice($results, 0, $limit);
    }

    public function profile(int $branchId, Carbon $now, ?string $phone = null, ?int $userId = null): ?array
    {
        $user = $userId ? User::find($userId) : null;
        if (!$user && $phone) {
            $normalized = $this->normalizePhone($phone);
            if ($normalized) {
                $user = User::query()->whereNotNull('phone')->get()->first(
                    fn ($row) => $this->normalizePhone($row->phone) === $normalized
                );
            }
        }

        $profiles = $this->buildProfiles($branchId, $now);
        $key = $this->patientKey($phone ?? $user?->phone, $userId ?? $user?->id, $user?->name);

        if (!$key) {
            return null;
        }

        if (!isset($profiles[$key]) && $user) {
            $key = $this->patientKey($user->phone, $user->id, $user->name);
        }

        $profile = $profiles[$key] ?? null;
        if (!$profile && $user) {
            $profile = [
                'key' => $key,
                'name' => $user->name,
                'phone' => $this->displayPhone($user->phone),
                'user_id' => $user->id,
                'visit_count' => 0,
                'total_spent' => 0.0,
                'first_visit' => null,
                'last_visit' => null,
                'visit_dates' => [],
                'has_due' => false,
                'active_admit' => false,
                'returned_after_gap' => false,
            ];
        }

        if (!$profile) {
            return null;
        }

        $segment = $this->classify($profile, $now);
        $due = $this->dueBreakdown($branchId, $phone ?? $user?->phone, $profile['name']);

        return [
            'patient' => [
                'name' => $profile['name'],
                'phone' => $profile['phone'] ?: '—',
                'user_id' => $profile['user_id'] ?? $user?->id,
                'age' => $user?->age,
                'gender' => $user?->gender,
                'blood_group' => $user?->blood_group,
                'address' => $user?->address,
            ],
            'segment' => $segment,
            'segment_label' => self::segmentLabels()[$segment] ?? ucfirst($segment),
            'prediction' => $this->predictionHint($profile, $segment, $now, false),
            'stats' => [
                'visit_count' => (int) $profile['visit_count'],
                'total_spent' => round((float) $profile['total_spent'], 2),
                'first_visit' => $profile['first_visit']?->format('d M Y') ?? '—',
                'last_visit' => $profile['last_visit']?->format('d M Y') ?? '—',
                'active_admit' => (bool) ($profile['active_admit'] ?? false),
            ],
            'due' => $due,
            'timeline' => $this->buildDetailedTimeline($branchId, $phone ?? $user?->phone, $user?->id),
            'modules' => $this->moduleCounts($branchId, $phone ?? $user?->phone, $user?->id),
        ];
    }

    private function dueBreakdown(int $branchId, ?string $phone, ?string $name): array
    {
        $normalized = $this->normalizePhone($phone);
        $query = Invoice::query()->where('branch_id', $branchId);

        if ($normalized) {
            $query->whereRaw("REPLACE(REPLACE(REPLACE(patient_phone, '-', ''), ' ', ''), '+', '') LIKE ?", ['%'.$normalized]);
        } elseif ($name) {
            $query->where('patient_name', $name);
        } else {
            return ['total' => 0.0, 'invoices' => []];
        }

        $invoices = $query->withSum('paidAmount', 'paid_amount')
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        $rows = [];
        $totalDue = 0.0;

        foreach ($invoices as $invoice) {
            $paid = (float) ($invoice->paid_amount_sum_paid_amount ?? 0);
            $due = max(0, (float) $invoice->total_amount - $paid);
            if ($due <= 0) {
                continue;
            }
            $totalDue += $due;
            $rows[] = [
                'invoice_number' => $invoice->invoice_number,
                'date' => $invoice->creation_date ? Carbon::parse($invoice->creation_date)->format('d M Y') : '—',
                'total' => round((float) $invoice->total_amount, 2),
                'paid' => round($paid, 2),
                'due' => round($due, 2),
                'invoice_id' => $invoice->id,
            ];
        }

        return [
            'total' => round($totalDue, 2),
            'invoices' => $rows,
        ];
    }

    private function buildDetailedTimeline(int $branchId, ?string $phone, ?int $userId): array
    {
        $normalized = $this->normalizePhone($phone);
        $events = [];

        $invoiceQuery = Invoice::query()->where('branch_id', $branchId);
        if ($normalized) {
            $invoiceQuery->whereRaw("REPLACE(REPLACE(REPLACE(patient_phone, '-', ''), ' ', ''), '+', '') LIKE ?", ['%'.$normalized]);
        }
        foreach ($invoiceQuery->orderByDesc('creation_date')->limit(30)->get() as $row) {
            $events[] = [
                'type' => 'invoice',
                'label' => 'Diagnostic Invoice',
                'icon' => 'fa-file-invoice-dollar',
                'date' => $row->creation_date ? Carbon::parse($row->creation_date)->format('d M Y H:i') : '—',
                'amount' => round((float) $row->total_amount, 2),
                'meta' => $row->invoice_number,
                'entity_id' => $row->id,
            ];
        }

        $opdQuery = DoctorSerial::query()->where('branch_id', $branchId);
        if ($normalized) {
            $opdQuery->whereRaw("REPLACE(REPLACE(REPLACE(patient_phone, '-', ''), ' ', ''), '+', '') LIKE ?", ['%'.$normalized]);
        }
        foreach ($opdQuery->orderByDesc('date')->limit(20)->get() as $row) {
            $events[] = [
                'type' => 'opd',
                'label' => 'OPD Serial',
                'icon' => 'fa-user-md',
                'date' => $row->date ? Carbon::parse($row->date)->format('d M Y') : '—',
                'amount' => round((float) $row->amount, 2),
                'meta' => 'Serial #'.$row->serial_number.' · '.$row->status,
                'entity_id' => $row->id,
            ];
        }

        if ($userId) {
            foreach (Recept::query()->where('branch_id', $branchId)->where('user_id', $userId)
                ->orderByDesc('created_date')->limit(20)->get() as $row) {
                $events[] = [
                    'type' => 'recept',
                    'label' => 'Hospital Recept',
                    'icon' => 'fa-receipt',
                    'date' => $row->created_date ? Carbon::parse($row->created_date)->format('d M Y') : '—',
                    'amount' => round((float) $row->total_amount, 2),
                    'meta' => 'Recept #'.$row->id,
                    'entity_id' => $row->id,
                ];
            }

            foreach (PharmacySale::query()->where('branch_id', $branchId)->where('customer_id', $userId)
                ->orderByDesc('sale_date')->limit(20)->get() as $row) {
                $events[] = [
                    'type' => 'pharmacy',
                    'label' => 'Pharmacy Sale',
                    'icon' => 'fa-pills',
                    'date' => $row->sale_date ? Carbon::parse($row->sale_date)->format('d M Y') : '—',
                    'amount' => round((float) $row->total_amount, 2),
                    'meta' => 'Sale #'.$row->id,
                    'entity_id' => $row->id,
                ];
            }

            foreach (Admit::query()->where('branch_id', $branchId)->where('user_id', $userId)
                ->orderByDesc('created_at')->limit(10)->get() as $row) {
                $events[] = [
                    'type' => 'admit',
                    'label' => $row->release_at ? 'IPD Discharged' : 'IPD Admitted',
                    'icon' => 'fa-procedures',
                    'date' => $row->created_at ? $row->created_at->format('d M Y') : '—',
                    'amount' => 0.0,
                    'meta' => $row->release_at ? 'Released '.$row->release_at : 'Active admission',
                    'entity_id' => $row->id,
                ];
            }
        }

        usort($events, fn ($a, $b) => strcmp($b['date'], $a['date']));

        return array_slice($events, 0, 40);
    }

    private function moduleCounts(int $branchId, ?string $phone, ?int $userId): array
    {
        $normalized = $this->normalizePhone($phone);

        $invoiceCount = 0;
        if ($normalized) {
            $invoiceCount = Invoice::where('branch_id', $branchId)
                ->whereRaw("REPLACE(REPLACE(REPLACE(patient_phone, '-', ''), ' ', ''), '+', '') LIKE ?", ['%'.$normalized])
                ->count();
        }

        $opdCount = 0;
        if ($normalized) {
            $opdCount = DoctorSerial::where('branch_id', $branchId)
                ->whereRaw("REPLACE(REPLACE(REPLACE(patient_phone, '-', ''), ' ', ''), '+', '') LIKE ?", ['%'.$normalized])
                ->count();
        }

        return [
            'invoices' => $invoiceCount,
            'opd' => $opdCount,
            'recepts' => $userId ? Recept::where('branch_id', $branchId)->where('user_id', $userId)->count() : 0,
            'pharmacy' => $userId ? PharmacySale::where('branch_id', $branchId)->where('customer_id', $userId)->count() : 0,
            'admits' => $userId ? Admit::where('branch_id', $branchId)->where('user_id', $userId)->count() : 0,
        ];
    }
}
