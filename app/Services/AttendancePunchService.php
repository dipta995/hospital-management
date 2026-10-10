<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceShift;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Single entry point for device punches (RFID, fingerprint, ESP API).
 * A punch closes the employee's open duty if it started within the max session length,
 * even across midnight; otherwise it opens a new duty on the shift's duty date.
 */
class AttendancePunchService
{
    public const RESULT_IN = 'in';
    public const RESULT_OUT = 'out';
    public const RESULT_DUPLICATE = 'duplicate';

    public function punch(Employee $employee, string $source, ?string $deviceData = null, ?Carbon $at = null): array
    {
        $now = ($at ?? Carbon::now('Asia/Dhaka'))->copy()->setTimezone('Asia/Dhaka');
        $settings = AttendanceSettings::forBranch($employee->branch_id);

        return DB::transaction(function () use ($employee, $source, $deviceData, $now, $settings) {
            Employee::whereKey($employee->id)->lockForUpdate()->first();

            $latest = Attendance::where('employee_id', $employee->id)
                ->where('in_time', '<=', $now)
                ->orderByDesc('in_time')
                ->first();

            if ($latest && $this->isDuplicate($latest, $now, $settings)) {
                $lastPunch = Carbon::parse($latest->out_time ?? $latest->in_time, 'Asia/Dhaka');

                return [
                    'result' => self::RESULT_DUPLICATE,
                    'attendance' => $latest,
                    'message' => 'Already recorded at ' . $lastPunch->format('h:i A') . '.',
                ];
            }

            $open = Attendance::where('employee_id', $employee->id)
                ->whereNull('out_time')
                ->where('in_time', '>=', $now->copy()->subHours($settings->maxSessionHours()))
                ->where('in_time', '<=', $now)
                ->orderByDesc('in_time')
                ->first();

            if ($open) {
                $open->out_time = $now->toDateTimeString();
                $open->save();

                return [
                    'result' => self::RESULT_OUT,
                    'attendance' => $open,
                    'message' => 'Attendance OUT marked.',
                ];
            }

            $isHourly = $settings->mode() === 'hourly';
            $shift = $this->employeeShift($employee);
            $attributes = [
                'employee_id' => $employee->id,
                'fingerprint_data' => $deviceData,
                'mode' => $isHourly ? 'hourly' : 'standard',
                'hour_slot' => (int) $now->format('G'),
                'date' => $this->dutyDate($now, $shift)->toDateString(),
                'in_time' => $now->toDateTimeString(),
                'out_time' => null,
            ];
            if (AttendanceSchemaService::hasShifts()) {
                $attributes['shift_id'] = $shift?->id;
                $attributes['source'] = $source;
            }

            return [
                'result' => self::RESULT_IN,
                'attendance' => Attendance::create($attributes),
                'message' => 'Attendance IN marked.',
            ];
        });
    }

    /**
     * Date the duty belongs to. A late arrival after midnight on an overnight shift
     * (e.g. 00:30 for a 22:00 - 08:00 shift) belongs to the previous day's duty.
     */
    public function dutyDate(Carbon $at, ?AttendanceShift $shift): Carbon
    {
        $date = $at->copy()->startOfDay();

        if ($shift && $shift->isOvernight() && $at->format('H:i') < substr($shift->end_time, 0, 5)) {
            return $date->subDay();
        }

        return $date;
    }

    public function employeeShift(Employee $employee): ?AttendanceShift
    {
        if (!AttendanceSchemaService::hasShifts() || !$employee->shift_id) {
            return null;
        }

        return AttendanceShift::find($employee->shift_id);
    }

    private function isDuplicate(Attendance $latest, Carbon $now, AttendanceSettings $settings): bool
    {
        $window = $settings->duplicatePunchMinutes();
        if ($window <= 0) {
            return false;
        }

        $lastPunch = Carbon::parse($latest->out_time ?? $latest->in_time, 'Asia/Dhaka');

        return $lastPunch->lte($now) && $lastPunch->diffInSeconds($now) < $window * 60;
    }
}
