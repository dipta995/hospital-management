<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceShift;
use App\Models\Employee;
use App\Models\EmployeeLeaveDay;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class EmployeeAttendanceSummaryService
{
    public const WEEK_DAYS = [
        0 => 'Sunday',
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
    ];

    private array $shiftCache = [];

    public function summarize(Employee $employee, string $month, string $year): array
    {
        $monthDate = Carbon::createFromFormat('F Y', "$month $year");
        $monthStart = $monthDate->copy()->startOfMonth()->startOfDay();
        $monthEnd = $monthDate->copy()->endOfMonth()->startOfDay();
        $daysInMonth = $monthDate->daysInMonth;
        $today = Carbon::now('Asia/Dhaka')->startOfDay();
        $countUntil = $monthEnd->lt($today) ? $monthEnd : $today;
        $lastWorkingDay = $employee->isResigned() && $employee->resigned_at
            ? $employee->resigned_at->copy()->startOfDay()
            : null;
        if ($lastWorkingDay && $lastWorkingDay->lt($countUntil)) {
            $countUntil = $lastWorkingDay->lt($monthStart) ? $monthStart->copy()->subDay() : $lastWorkingDay->copy();
        }

        $weeklyOffDays = $this->normalizeWeeklyOffDays($employee->weekly_off_days ?? []);
        $settings = AttendanceSettings::forBranch($employee->branch_id);
        $now = Carbon::now('Asia/Dhaka');
        $employeeShift = $this->shiftFor($employee);
        $workingHoursPerDay = $employeeShift
            ? round($employeeShift->expectedMinutes((float) ($employee->working_hours_per_day ?? 8)) / 60, 2)
            : (float) ($employee->working_hours_per_day ?? 8);

        $attendanceRecords = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->get();

        $recordsByDate = $attendanceRecords->groupBy(
            fn ($record) => $this->normalizeDateKey($record->date)
        );

        $leaveRecords = collect();
        if ($this->leaveTableExists()) {
            $leaveRecords = EmployeeLeaveDay::where('employee_id', $employee->id)
                ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->get()
                ->keyBy(fn ($leave) => $this->normalizeDateKey($leave->date));
        }

        $offDayDates = [];
        $leaveDayDates = [];
        $presentDayDates = [];
        $absenceDayDates = [];
        $upcomingDayDates = [];
        $dailyBreakdown = [];

        $totalHours = 0;
        $weeklyOffCount = 0;
        $weeklyOffCountElapsed = 0;
        $leaveCount = 0;
        $paidLeaveCount = 0;
        $unpaidLeaveCount = 0;
        $presentCount = 0;
        $absenceCount = 0;
        $incompleteSessions = 0;
        $expectedWorkingDaysElapsed = 0;
        $lateCount = 0;
        $lateMinutes = 0;
        $earlyLeaveCount = 0;
        $earlyLeaveMinutes = 0;
        $overtimeMinutes = 0;
        $missingOutDates = [];

        foreach (CarbonPeriod::create($monthStart, $monthEnd) as $day) {
            $dateKey = $day->toDateString();
            $dayName = self::WEEK_DAYS[$day->dayOfWeek];
            $isWeeklyOff = in_array($dayName, $weeklyOffDays, true);
            $isFutureDay = $day->gt($countUntil);
            $isElapsedDay = $day->lte($countUntil);
            $leave = $leaveRecords->get($dateKey);
            $dayRecords = $recordsByDate->get($dateKey, collect());
            $hasAttendance = $dayRecords->isNotEmpty();

            $evaluation = $hasAttendance
                ? $this->evaluateDay($employee, $day->copy(), $dayRecords, $this->shiftFor($employee, $dayRecords), $settings, $now)
                : null;
            $dayHours = $evaluation ? $evaluation['credited_minutes'] / 60 : 0;
            $totalHours += $dayHours;

            if ($evaluation) {
                $incompleteSessions += $evaluation['open_sessions'] + $evaluation['missing_out'];
                if ($evaluation['missing_out'] > 0) {
                    $missingOutDates[] = $dateKey;
                }
                if ($evaluation['late_minutes'] > 0) {
                    $lateCount++;
                    $lateMinutes += $evaluation['late_minutes'];
                }
                if ($evaluation['early_minutes'] > 0) {
                    $earlyLeaveCount++;
                    $earlyLeaveMinutes += $evaluation['early_minutes'];
                }
                $overtimeMinutes += $evaluation['overtime_minutes'];
            }
            if ($evaluation && $evaluation['counts_as_absent']) {
                $hasAttendance = false;
            }

            if ($isWeeklyOff) {
                $weeklyOffCount++;
                $offDayDates[] = $dateKey;
                if ($isElapsedDay) {
                    $weeklyOffCountElapsed++;
                }
            }

            if ($isFutureDay && $lastWorkingDay && $day->gt($lastWorkingDay)) {
                $status = 'resigned';
            } elseif ($isFutureDay) {
                $status = 'upcoming';
                $upcomingDayDates[] = $dateKey;
            } elseif ($hasAttendance) {
                $status = 'present';
                $presentCount++;
                $presentDayDates[] = $dateKey;
            } elseif ($leave) {
                $status = 'leave';
                $leaveCount++;
                $leaveDayDates[] = $dateKey;
                if ($leave->is_paid) {
                    $paidLeaveCount++;
                } else {
                    $unpaidLeaveCount++;
                }
            } elseif ($isWeeklyOff) {
                $status = 'off_day';
            } else {
                $status = 'absence';
                $absenceCount++;
                $absenceDayDates[] = $dateKey;
            }

            if ($isElapsedDay && !$isWeeklyOff) {
                $expectedWorkingDaysElapsed++;
            }

            $dailyBreakdown[] = [
                'date' => $dateKey,
                'day_name' => $dayName,
                'status' => $status,
                'leave_type' => $leave?->type,
                'leave_type_label' => $leave?->type_label,
                'hours' => round($dayHours, 2),
                'sessions' => $dayRecords->count(),
                'is_paid_leave' => $leave ? $leave->is_paid : null,
                'missing_out' => $evaluation['missing_out'] ?? 0,
                'late_minutes' => $evaluation['late_minutes'] ?? 0,
                'early_minutes' => $evaluation['early_minutes'] ?? 0,
                'overtime_minutes' => $evaluation['overtime_minutes'] ?? 0,
            ];
        }

        $expectedWorkingDays = max(0, $daysInMonth - $weeklyOffCount);
        $attendanceRate = $expectedWorkingDaysElapsed > 0
            ? round(($presentCount / $expectedWorkingDaysElapsed) * 100, 1)
            : 0;

        $expectedHours = $expectedWorkingDaysElapsed * $workingHoursPerDay;
        $missingHours = max(0, round($expectedHours - $totalHours, 2));

        $leaveUsedYtd = EmployeeLeaveDay::where('employee_id', $employee->id)
            ->whereYear('date', $year)
            ->count();

        $annualQuota = (int) ($employee->annual_leave_quota ?? 12);
        $remainingLeaveQuota = max(0, $annualQuota - $leaveUsedYtd);

        return [
            'daysInMonth' => $daysInMonth,
            'weeklyOffCount' => $weeklyOffCount,
            'expectedWorkingDays' => $expectedWorkingDaysElapsed,
            'expectedWorkingDaysFullMonth' => $expectedWorkingDays,
            'upcomingDayCount' => count($upcomingDayDates),
            'presentCount' => $presentCount,
            'leaveCount' => $leaveCount,
            'paidLeaveCount' => $paidLeaveCount,
            'unpaidLeaveCount' => $unpaidLeaveCount,
            'absenceCount' => $absenceCount,
            'totalHours' => round($totalHours, 2),
            'expectedHours' => round($expectedHours, 2),
            'missingHours' => $missingHours,
            'workingHoursPerDay' => $workingHoursPerDay,
            'attendanceRate' => $attendanceRate,
            'incompleteSessions' => $incompleteSessions,
            'missingOutCount' => count($missingOutDates),
            'missingOutDates' => $missingOutDates,
            'lateCount' => $lateCount,
            'lateMinutes' => $lateMinutes,
            'earlyLeaveCount' => $earlyLeaveCount,
            'earlyLeaveMinutes' => $earlyLeaveMinutes,
            'overtimeMinutes' => $overtimeMinutes,
            'overtimeHours' => round($overtimeMinutes / 60, 2),
            'shiftName' => $employeeShift?->name,
            'annualLeaveQuota' => $annualQuota,
            'leaveUsedYtd' => $leaveUsedYtd,
            'remainingLeaveQuota' => $remainingLeaveQuota,
            'offDayDates' => $offDayDates,
            'leaveDayDates' => $leaveDayDates,
            'presentDayDates' => $presentDayDates,
            'absenceDayDates' => $absenceDayDates,
            'upcomingDayDates' => $upcomingDayDates,
            'dailyBreakdown' => $dailyBreakdown,
            'leaveByType' => $this->groupLeaveByType($leaveRecords),
            // Legacy keys used by salary sheet
            'totalDays' => $presentCount,
            'expectedDays' => $expectedWorkingDays,
            'missingDays' => $absenceCount,
            'recordCount' => $attendanceRecords->count(),
        ];
    }

    /**
     * One row per employee for a single day, using the same status rules as summarize().
     */
    public function dailySheet(Collection $employees, Carbon $date): array
    {
        $dateKey = $date->toDateString();
        $isFutureDay = $date->copy()->startOfDay()->gt(Carbon::now('Asia/Dhaka')->startOfDay());
        $employeeIds = $employees->pluck('id');

        $recordsByEmployee = Attendance::whereIn('employee_id', $employeeIds)
            ->whereDate('date', $dateKey)
            ->orderBy('in_time')
            ->get()
            ->groupBy('employee_id');

        $leavesByEmployee = $this->leaveTableExists()
            ? EmployeeLeaveDay::whereIn('employee_id', $employeeIds)->whereDate('date', $dateKey)->get()->keyBy('employee_id')
            : collect();

        $dayName = self::WEEK_DAYS[$date->dayOfWeek];
        $totals = [
            'employees' => 0, 'present' => 0, 'open' => 0, 'leave' => 0, 'off_day' => 0, 'absence' => 0, 'upcoming' => 0,
            'hours' => 0.0, 'late' => 0, 'early' => 0, 'missing_out' => 0, 'overtime_minutes' => 0,
        ];
        $rows = [];
        $now = Carbon::now('Asia/Dhaka');

        foreach ($employees as $employee) {
            $sessions = $recordsByEmployee->get($employee->id, collect())->values();
            $leave = $leavesByEmployee->get($employee->id);
            $isWeeklyOff = in_array($dayName, $this->normalizeWeeklyOffDays($employee->weekly_off_days ?? []), true);
            $shift = $this->shiftFor($employee, $sessions);
            $evaluation = $this->evaluateDay(
                $employee,
                $date->copy()->startOfDay(),
                $sessions,
                $shift,
                AttendanceSettings::forBranch($employee->branch_id),
                $now
            );
            $expectedHours = round($evaluation['expected_minutes'] / 60, 2);
            $workedMinutes = $evaluation['worked_minutes'];
            $openSessions = $evaluation['open_sessions'];

            if ($sessions->isNotEmpty() && !$evaluation['counts_as_absent']) {
                $status = 'present';
            } elseif ($leave) {
                $status = 'leave';
            } elseif ($isWeeklyOff) {
                $status = 'off_day';
            } elseif ($isFutureDay) {
                $status = 'upcoming';
            } else {
                $status = 'absence';
            }

            $firstIn = $sessions->pluck('in_time')->filter()->first();
            $lastOut = $sessions->pluck('out_time')->filter()->sort()->last();

            $rows[] = [
                'employee' => $employee,
                'status' => $status,
                'leave_label' => $leave?->type_label,
                'is_paid_leave' => $leave ? (bool) $leave->is_paid : null,
                'sessions' => $sessions,
                'first_in' => $firstIn ? Carbon::parse($firstIn) : null,
                'last_out' => $lastOut ? Carbon::parse($lastOut) : null,
                'open_sessions' => $openSessions,
                'missing_out' => $evaluation['missing_out'],
                'worked_minutes' => $workedMinutes,
                'expected_hours' => $expectedHours,
                'short_minutes' => $status === 'present' && $openSessions === 0 && $evaluation['missing_out'] === 0
                    ? max(0, $evaluation['expected_minutes'] - $workedMinutes)
                    : 0,
                'late_minutes' => $evaluation['late_minutes'],
                'early_minutes' => $evaluation['early_minutes'],
                'overtime_minutes' => $evaluation['overtime_minutes'],
                'shift' => $shift,
                'notes' => $sessions->pluck('note')->filter()->implode('; '),
            ];

            $totals['employees']++;
            $totals[$status]++;
            if ($openSessions > 0) {
                $totals['open']++;
            }
            if ($evaluation['missing_out'] > 0) {
                $totals['missing_out']++;
            }
            if ($evaluation['late_minutes'] > 0) {
                $totals['late']++;
            }
            if ($evaluation['early_minutes'] > 0) {
                $totals['early']++;
            }
            $totals['overtime_minutes'] += $evaluation['overtime_minutes'];
            $totals['hours'] += $workedMinutes / 60;
        }

        $totals['hours'] = round($totals['hours'], 2);

        return [
            'date' => $date,
            'day_name' => $dayName,
            'rows' => $rows,
            'totals' => $totals,
        ];
    }

    public static function formatMinutes(int $minutes): string
    {
        return sprintf('%d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

    public function summarizeMany(Collection $employees, string $month, string $year): array
    {
        $summaries = [];
        foreach ($employees as $employee) {
            $summaries[$employee->id] = $this->summarize($employee, $month, $year);
        }

        return $summaries;
    }

    public function calculateAbsenceDeduction(Employee $employee, array $summary): float
    {
        $absenceCount = (int) ($summary['absenceCount'] ?? 0);
        $expectedWorkingDays = (int) ($summary['expectedWorkingDaysFullMonth'] ?? $summary['expectedWorkingDays'] ?? 0);

        if ($expectedWorkingDays <= 0 || $absenceCount <= 0 || !$employee->salary) {
            return 0;
        }

        $dailyRate = $employee->salary / $expectedWorkingDays;

        return round($dailyRate * $absenceCount, 2);
    }

    public function calculateHourlyDeduction(Employee $employee, array $summary): float
    {
        $expectedHours = $summary['expectedHours'] ?? 0;
        $missingHours = $summary['missingHours'] ?? 0;

        if ($expectedHours <= 0 || $missingHours <= 0 || !$employee->salary) {
            return 0;
        }

        $hourlyRate = $employee->salary / $expectedHours;

        return round($hourlyRate * $missingHours, 2);
    }

    public function normalizeWeeklyOffDays($value): array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (!is_array($decoded)) {
                $decoded = array_map('trim', explode(',', $value));
            }
            $value = $decoded;
        }

        if (!is_array($value)) {
            return [];
        }

        $lookup = [];
        foreach (self::WEEK_DAYS as $dayName) {
            $lookup[strtolower($dayName)] = $dayName;
        }

        $normalized = [];
        foreach ($value as $day) {
            if (is_numeric($day) && isset(self::WEEK_DAYS[(int) $day])) {
                $normalized[] = self::WEEK_DAYS[(int) $day];
                continue;
            }

            $key = strtolower(trim((string) $day));
            if (isset($lookup[$key])) {
                $normalized[] = $lookup[$key];
            }
        }

        return array_values(array_unique($normalized));
    }

    /**
     * Worked time, late arrival, early leave and overtime for one duty date.
     * Sessions are grouped by the date the duty started, so a night duty stays on one day.
     * An open session older than the max session length is a "missing OUT" and is credited per policy.
     */
    public function evaluateDay(
        Employee $employee,
        Carbon $date,
        Collection $sessions,
        ?AttendanceShift $shift,
        AttendanceSettings $settings,
        ?Carbon $now = null
    ): array {
        $now ??= Carbon::now('Asia/Dhaka');
        $staleBefore = $now->copy()->subHours($settings->maxSessionHours());
        $fallbackHours = (float) ($employee->working_hours_per_day ?? 8);
        $expectedMinutes = $shift ? $shift->expectedMinutes($fallbackHours) : (int) round($fallbackHours * 60);

        $rawMinutes = 0;
        $closed = 0;
        $open = 0;
        $missingOut = 0;
        $firstIn = null;
        $lastOut = null;

        foreach ($sessions as $session) {
            if (!$session->in_time) {
                continue;
            }
            $in = Carbon::parse($session->in_time, 'Asia/Dhaka');
            if (!$firstIn || $in->lt($firstIn)) {
                $firstIn = $in;
            }

            if ($session->out_time) {
                $out = Carbon::parse($session->out_time, 'Asia/Dhaka');
                $rawMinutes += max(0, $in->diffInMinutes($out, false));
                $closed++;
                if (!$lastOut || $out->gt($lastOut)) {
                    $lastOut = $out;
                }
            } elseif ($in->lt($staleBefore)) {
                $missingOut++;
            } else {
                $open++;
            }
        }

        // Unpaid break is only deducted from a real working day (more than 4 hours punched).
        $breakMinutes = $shift && $rawMinutes > 240 ? (int) $shift->break_minutes : 0;
        $workedMinutes = max(0, $rawMinutes - $breakMinutes);

        $creditedMinutes = $workedMinutes;
        $countsAsAbsent = false;
        if ($missingOut > 0) {
            $policy = $settings->missingOutPolicy();
            if ($policy === AttendanceSettings::MISSING_OUT_SHIFT) {
                $creditedMinutes = max($workedMinutes, $expectedMinutes);
            } elseif ($policy === AttendanceSettings::MISSING_OUT_ABSENT && $closed === 0 && $open === 0) {
                $countsAsAbsent = true;
            }
        }

        $lateMinutes = 0;
        $earlyMinutes = 0;
        $overtimeMinutes = 0;
        $isComplete = $closed > 0 && $open === 0 && $missingOut === 0;

        if ($shift && $shift->hasFixedTime() && $firstIn) {
            $lateBy = $shift->startsAt($date)->diffInMinutes($firstIn, false);
            if ($lateBy > $settings->lateGraceMinutes()) {
                $lateMinutes = $lateBy;
            }
        }

        if ($isComplete && $shift && $shift->hasFixedTime() && $lastOut) {
            $earlyBy = $lastOut->diffInMinutes($shift->endsAt($date), false);
            if ($earlyBy > $settings->earlyLeaveGraceMinutes()) {
                $earlyMinutes = $earlyBy;
            }
        }

        if ($isComplete && $expectedMinutes > 0) {
            $extra = $workedMinutes - $expectedMinutes;
            if ($extra > 0 && $extra >= $settings->overtimeMinMinutes()) {
                $overtimeMinutes = $extra;
            }
        }

        return [
            'worked_minutes' => $workedMinutes,
            'credited_minutes' => $creditedMinutes,
            'expected_minutes' => $expectedMinutes,
            'break_minutes' => $breakMinutes,
            'closed_sessions' => $closed,
            'open_sessions' => $open,
            'missing_out' => $missingOut,
            'first_in' => $firstIn,
            'last_out' => $lastOut,
            'late_minutes' => $lateMinutes,
            'early_minutes' => $earlyMinutes,
            'overtime_minutes' => $overtimeMinutes,
            'counts_as_absent' => $countsAsAbsent,
        ];
    }

    /**
     * Shift for a duty: the shift recorded on the punch (so later shift changes don't rewrite history),
     * otherwise the employee's current shift.
     */
    public function shiftFor(Employee $employee, ?Collection $sessions = null): ?AttendanceShift
    {
        if (!AttendanceSchemaService::hasShifts()) {
            return null;
        }

        $shiftId = $sessions?->pluck('shift_id')->filter()->first() ?? $employee->shift_id;
        if (!$shiftId) {
            return null;
        }

        if (!array_key_exists($shiftId, $this->shiftCache)) {
            $this->shiftCache[$shiftId] = AttendanceShift::find($shiftId);
        }

        return $this->shiftCache[$shiftId];
    }

    /**
     * Salary cut for repeated late arrival: every N lates = X day(s) of salary.
     */
    public function calculateLatePenalty(Employee $employee, array $summary): float
    {
        $settings = AttendanceSettings::forBranch($employee->branch_id);
        $expectedWorkingDays = (int) ($summary['expectedWorkingDaysFullMonth'] ?? 0);

        if (!$settings->latePenaltyEnabled() || $expectedWorkingDays <= 0 || !$employee->salary) {
            return 0;
        }

        $penaltyDays = intdiv((int) ($summary['lateCount'] ?? 0), $settings->latePenaltyCount()) * $settings->latePenaltyDays();

        return round(($employee->salary / $expectedWorkingDays) * $penaltyDays, 2);
    }

    public function calculateOvertimePay(Employee $employee, array $summary): float
    {
        $settings = AttendanceSettings::forBranch($employee->branch_id);
        $hours = ((int) ($summary['overtimeMinutes'] ?? 0)) / 60;

        if (!$settings->overtimePayEnabled() || $hours <= 0) {
            return 0;
        }

        if ($settings->overtimeRateMode() === 'fixed') {
            return round($hours * $settings->overtimeFixedRate(), 2);
        }

        $monthHours = ((int) ($summary['expectedWorkingDaysFullMonth'] ?? 0)) * (float) ($summary['workingHoursPerDay'] ?? 8);
        if ($monthHours <= 0 || !$employee->salary) {
            return 0;
        }

        return round($hours * ($employee->salary / $monthHours) * $settings->overtimeMultiplier(), 2);
    }

    private function normalizeDateKey($date): string
    {
        return Carbon::parse($date)->toDateString();
    }

    private function leaveTableExists(): bool
    {
        try {
            return \Illuminate\Support\Facades\Schema::hasTable('employee_leave_days');
        } catch (\Throwable) {
            return false;
        }
    }

    private function groupLeaveByType(Collection $leaveRecords): array
    {
        $grouped = [];

        foreach ($leaveRecords as $leave) {
            $type = $leave->type;
            if (!isset($grouped[$type])) {
                $grouped[$type] = [
                    'type' => $type,
                    'label' => $leave->type_label,
                    'count' => 0,
                ];
            }
            $grouped[$type]['count']++;
        }

        return array_values($grouped);
    }
}
