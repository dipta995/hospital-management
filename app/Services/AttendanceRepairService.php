<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Re-pairs old device punches with the current rules (night duty across midnight, repeat-tap filter).
 * Old punches were paired per calendar date, so an OUT after midnight became a new IN and every
 * later pair shifted by one. Manual entries (with a note or source "manual") are never touched.
 */
class AttendanceRepairService
{
    public function __construct(private AttendancePunchService $punchService) {}

    /**
     * @return array<int, array{employee: Employee, before: array, after: array, record_ids: array, ignored: int}>
     */
    public function plan(Collection $employees, Carbon $start, Carbon $end): array
    {
        $plans = [];

        foreach ($employees as $employee) {
            $plan = $this->planFor($employee, $start, $end);
            if ($plan) {
                $plans[$employee->id] = $plan;
            }
        }

        return $plans;
    }

    public function planFor(Employee $employee, Carbon $start, Carbon $end): ?array
    {
        $settings = AttendanceSettings::forBranch($employee->branch_id);
        $hasShifts = AttendanceSchemaService::hasShifts();

        $records = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('in_time')
            ->get()
            ->reject(fn ($record) => $record->note || ($hasShifts && $record->source === 'manual'))
            ->values();

        if ($records->isEmpty()) {
            return null;
        }

        $punches = collect();
        foreach ($records as $record) {
            $punches->push(['at' => Carbon::parse($record->in_time, 'Asia/Dhaka'), 'record' => $record]);
            if ($record->out_time) {
                $punches->push(['at' => Carbon::parse($record->out_time, 'Asia/Dhaka'), 'record' => $record]);
            }
        }
        $punches = $punches->sortBy(fn ($punch) => $punch['at']->timestamp)->values();

        $shift = $this->punchService->employeeShift($employee);
        $maxSeconds = $settings->maxSessionHours() * 3600;
        $duplicateSeconds = $settings->duplicatePunchMinutes() * 60;

        $sessions = [];
        $current = null;
        $lastAccepted = null;
        $ignored = 0;

        foreach ($punches as $punch) {
            $at = $punch['at'];
            if ($lastAccepted && $duplicateSeconds > 0 && $lastAccepted->diffInSeconds($at) < $duplicateSeconds) {
                $ignored++;
                continue;
            }
            $lastAccepted = $at;

            if ($current && $current['in']->diffInSeconds($at) <= $maxSeconds) {
                $current['out'] = $at;
                $sessions[] = $current;
                $current = null;
                continue;
            }

            if ($current) {
                $sessions[] = $current;
            }
            $current = [
                'in' => $at,
                'out' => null,
                'date' => $this->punchService->dutyDate($at, $shift)->toDateString(),
                'record' => $punch['record'],
            ];
        }
        if ($current) {
            $sessions[] = $current;
        }

        $before = $records->map(fn ($record) => [
            'date' => Carbon::parse($record->date)->toDateString(),
            'in' => Carbon::parse($record->in_time, 'Asia/Dhaka'),
            'out' => $record->out_time ? Carbon::parse($record->out_time, 'Asia/Dhaka') : null,
        ])->all();

        $signature = fn (array $rows) => collect($rows)
            ->map(fn ($row) => $row['date'] . '|' . $row['in']->toDateTimeString() . '|' . ($row['out']?->toDateTimeString() ?? '-'))
            ->implode(',');

        if ($signature($before) === $signature($sessions)) {
            return null;
        }

        return [
            'employee' => $employee,
            'before' => $before,
            'after' => $sessions,
            'record_ids' => $records->pluck('id')->all(),
            'ignored' => $ignored,
        ];
    }

    public function apply(array $plan): int
    {
        $hasShifts = AttendanceSchemaService::hasShifts();

        return DB::transaction(function () use ($plan, $hasShifts) {
            $employee = $plan['employee'];

            foreach ($plan['after'] as $session) {
                $source = $session['record'];
                $attributes = [
                    'employee_id' => $employee->id,
                    'fingerprint_data' => $source->fingerprint_data,
                    'mode' => $source->mode ?? 'standard',
                    'hour_slot' => (int) $session['in']->format('G'),
                    'date' => $session['date'],
                    'in_time' => $session['in']->toDateTimeString(),
                    'out_time' => $session['out']?->toDateTimeString(),
                ];
                if ($hasShifts) {
                    $attributes['shift_id'] = $source->shift_id ?? $employee->shift_id;
                    $attributes['source'] = $source->source;
                }
                $sessionsToCreate[] = $attributes;
            }

            Attendance::whereIn('id', $plan['record_ids'])->delete();
            foreach ($sessionsToCreate ?? [] as $attributes) {
                Attendance::create($attributes);
            }

            return count($sessionsToCreate ?? []);
        });
    }
}
