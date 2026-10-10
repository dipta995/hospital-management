<?php

namespace App\Services;

use App\Models\Setting;

/**
 * Branch-level attendance policy read from the settings table, with sane defaults.
 */
class AttendanceSettings
{
    public const MISSING_OUT_REVIEW = 'review';
    public const MISSING_OUT_SHIFT = 'shift';
    public const MISSING_OUT_ABSENT = 'absent';

    public const DEFAULTS = [
        'attendance_mode' => 'standard',
        'attendance_grace_minutes' => 10,
        'attendance_early_leave_grace_minutes' => 10,
        'attendance_duplicate_punch_minutes' => 2,
        'attendance_max_session_hours' => 16,
        'attendance_missing_out_policy' => self::MISSING_OUT_REVIEW,
        'attendance_overtime_min_minutes' => 30,
        'attendance_late_penalty_enabled' => 0,
        'attendance_late_penalty_count' => 3,
        'attendance_late_penalty_days' => 1,
        'attendance_overtime_pay_enabled' => 0,
        'attendance_overtime_rate_mode' => 'salary_based',
        'attendance_overtime_multiplier' => 1,
        'attendance_overtime_fixed_rate' => 0,
    ];

    private static array $cache = [];

    private function __construct(private array $values) {}

    public static function forBranch($branchId): self
    {
        $db = (string) config('database.connections.' . config('database.default') . '.database');
        $key = $db . ':' . (int) $branchId;

        if (!isset(self::$cache[$key])) {
            $stored = Setting::where('branch_id', $branchId)
                ->whereIn('key', array_keys(self::DEFAULTS))
                ->pluck('value', 'key')
                ->all();

            $values = self::DEFAULTS;
            foreach ($stored as $name => $value) {
                if ($value !== null && $value !== '') {
                    $values[$name] = $value;
                }
            }
            self::$cache[$key] = new self($values);
        }

        return self::$cache[$key];
    }

    public static function flush(): void
    {
        self::$cache = [];
    }

    public function mode(): string
    {
        return $this->values['attendance_mode'] === 'hourly' ? 'hourly' : 'standard';
    }

    public function lateGraceMinutes(): int
    {
        return max(0, (int) $this->values['attendance_grace_minutes']);
    }

    public function earlyLeaveGraceMinutes(): int
    {
        return max(0, (int) $this->values['attendance_early_leave_grace_minutes']);
    }

    public function duplicatePunchMinutes(): int
    {
        return max(0, (int) $this->values['attendance_duplicate_punch_minutes']);
    }

    public function maxSessionHours(): int
    {
        return min(48, max(4, (int) $this->values['attendance_max_session_hours']));
    }

    public function missingOutPolicy(): string
    {
        $policy = $this->values['attendance_missing_out_policy'];

        return in_array($policy, [self::MISSING_OUT_REVIEW, self::MISSING_OUT_SHIFT, self::MISSING_OUT_ABSENT], true)
            ? $policy
            : self::MISSING_OUT_REVIEW;
    }

    public function overtimeMinMinutes(): int
    {
        return max(0, (int) $this->values['attendance_overtime_min_minutes']);
    }

    public function latePenaltyEnabled(): bool
    {
        return (bool) (int) $this->values['attendance_late_penalty_enabled'];
    }

    public function latePenaltyCount(): int
    {
        return max(1, (int) $this->values['attendance_late_penalty_count']);
    }

    public function latePenaltyDays(): float
    {
        return max(0, (float) $this->values['attendance_late_penalty_days']);
    }

    public function overtimePayEnabled(): bool
    {
        return (bool) (int) $this->values['attendance_overtime_pay_enabled'];
    }

    public function overtimeRateMode(): string
    {
        return $this->values['attendance_overtime_rate_mode'] === 'fixed' ? 'fixed' : 'salary_based';
    }

    public function overtimeMultiplier(): float
    {
        return max(0, (float) $this->values['attendance_overtime_multiplier']);
    }

    public function overtimeFixedRate(): float
    {
        return max(0, (float) $this->values['attendance_overtime_fixed_rate']);
    }
}
