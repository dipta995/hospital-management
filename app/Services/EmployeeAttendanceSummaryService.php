<?php

namespace App\Services;

use App\Models\Attendance;
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
        $workingHoursPerDay = (float) ($employee->working_hours_per_day ?? 8);

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

        foreach (CarbonPeriod::create($monthStart, $monthEnd) as $day) {
            $dateKey = $day->toDateString();
            $dayName = self::WEEK_DAYS[$day->dayOfWeek];
            $isWeeklyOff = in_array($dayName, $weeklyOffDays, true);
            $isFutureDay = $day->gt($countUntil);
            $isElapsedDay = $day->lte($countUntil);
            $leave = $leaveRecords->get($dateKey);
            $dayRecords = $recordsByDate->get($dateKey, collect());
            $hasAttendance = $dayRecords->isNotEmpty();

            $dayHours = $this->calculateDayHours($dayRecords, $workingHoursPerDay);
            $totalHours += $dayHours;

            foreach ($dayRecords as $record) {
                if ($record->in_time && !$record->out_time) {
                    $incompleteSessions++;
                }
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
        $totals = ['employees' => 0, 'present' => 0, 'open' => 0, 'leave' => 0, 'off_day' => 0, 'absence' => 0, 'upcoming' => 0, 'hours' => 0.0];
        $rows = [];

        foreach ($employees as $employee) {
            $sessions = $recordsByEmployee->get($employee->id, collect())->values();
            $leave = $leavesByEmployee->get($employee->id);
            $isWeeklyOff = in_array($dayName, $this->normalizeWeeklyOffDays($employee->weekly_off_days ?? []), true);
            $expectedHours = (float) ($employee->working_hours_per_day ?? 8);

            $workedMinutes = 0;
            $openSessions = 0;
            foreach ($sessions as $session) {
                if ($session->in_time && $session->out_time) {
                    $workedMinutes += max(0, Carbon::parse($session->in_time)->diffInMinutes(Carbon::parse($session->out_time), false));
                } elseif ($session->in_time) {
                    $openSessions++;
                }
            }

            if ($sessions->isNotEmpty()) {
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
                'worked_minutes' => $workedMinutes,
                'expected_hours' => $expectedHours,
                'short_minutes' => $status === 'present' && $openSessions === 0
                    ? max(0, (int) round($expectedHours * 60) - $workedMinutes)
                    : 0,
                'notes' => $sessions->pluck('note')->filter()->implode('; '),
            ];

            $totals['employees']++;
            $totals[$status]++;
            if ($openSessions > 0) {
                $totals['open']++;
            }
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

    private function calculateDayHours(Collection $dayRecords, float $defaultHours): float
    {
        $dayHours = 0;

        foreach ($dayRecords as $record) {
            if ($record->in_time && $record->out_time) {
                $inTime = Carbon::parse($record->in_time);
                $outTime = Carbon::parse($record->out_time);
                $dayHours += max(0, $outTime->diffInMinutes($inTime, false) / 60);
            }
        }

        if ($dayHours === 0 && $dayRecords->isNotEmpty()) {
            $dayHours = $defaultHours;
        }

        return $dayHours;
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
