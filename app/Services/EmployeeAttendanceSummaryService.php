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
        $monthStart = $monthDate->copy()->startOfMonth();
        $monthEnd = $monthDate->copy()->endOfMonth();
        $daysInMonth = $monthDate->daysInMonth;

        $weeklyOffDays = $this->normalizeWeeklyOffDays($employee->weekly_off_days ?? []);
        $workingHoursPerDay = (float) ($employee->working_hours_per_day ?? 8);

        $attendanceRecords = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->get();

        $recordsByDate = $attendanceRecords->groupBy(fn ($record) => Carbon::parse($record->date)->toDateString());

        $leaveRecords = EmployeeLeaveDay::where('employee_id', $employee->id)
            ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->get()
            ->keyBy(fn ($leave) => Carbon::parse($leave->date)->toDateString());

        $offDayDates = [];
        $leaveDayDates = [];
        $presentDayDates = [];
        $absenceDayDates = [];
        $dailyBreakdown = [];

        $totalHours = 0;
        $weeklyOffCount = 0;
        $leaveCount = 0;
        $paidLeaveCount = 0;
        $unpaidLeaveCount = 0;
        $presentCount = 0;
        $absenceCount = 0;
        $incompleteSessions = 0;

        foreach (CarbonPeriod::create($monthStart, $monthEnd) as $day) {
            $dateKey = $day->toDateString();
            $dayName = self::WEEK_DAYS[$day->dayOfWeek];
            $isWeeklyOff = in_array($dayName, $weeklyOffDays, true);
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

            $status = 'working';
            if ($isWeeklyOff) {
                $status = 'off_day';
                $weeklyOffCount++;
                $offDayDates[] = $dateKey;
            } elseif ($leave) {
                $status = 'leave';
                $leaveCount++;
                $leaveDayDates[] = $dateKey;
                if ($leave->is_paid) {
                    $paidLeaveCount++;
                } else {
                    $unpaidLeaveCount++;
                }
            } elseif ($hasAttendance) {
                $status = 'present';
                $presentCount++;
                $presentDayDates[] = $dateKey;
            } else {
                $status = 'absence';
                $absenceCount++;
                $absenceDayDates[] = $dateKey;
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
        $attendanceRate = $expectedWorkingDays > 0
            ? round(($presentCount / $expectedWorkingDays) * 100, 1)
            : 0;

        $expectedHours = $expectedWorkingDays * $workingHoursPerDay;
        $missingHours = max(0, round($expectedHours - $totalHours, 2));

        $leaveUsedYtd = EmployeeLeaveDay::where('employee_id', $employee->id)
            ->whereYear('date', $year)
            ->count();

        $annualQuota = (int) ($employee->annual_leave_quota ?? 12);
        $remainingLeaveQuota = max(0, $annualQuota - $leaveUsedYtd);

        return [
            'daysInMonth' => $daysInMonth,
            'weeklyOffCount' => $weeklyOffCount,
            'expectedWorkingDays' => $expectedWorkingDays,
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
            'dailyBreakdown' => $dailyBreakdown,
            'leaveByType' => $this->groupLeaveByType($leaveRecords),
            // Legacy keys used by salary sheet
            'totalDays' => $presentCount,
            'expectedDays' => $expectedWorkingDays,
            'missingDays' => $absenceCount,
            'recordCount' => $attendanceRecords->count(),
        ];
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
        $expectedWorkingDays = $summary['expectedWorkingDays'] ?? 0;
        $absenceCount = $summary['absenceCount'] ?? 0;

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
            $value = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($value)) {
            return [];
        }

        $valid = array_values(self::WEEK_DAYS);

        return array_values(array_unique(array_filter($value, fn ($day) => in_array($day, $valid, true))));
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
