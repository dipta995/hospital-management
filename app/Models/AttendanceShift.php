<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AttendanceShift extends Model
{
    protected $fillable = [
        'branch_id',
        'name',
        'start_time',
        'end_time',
        'break_minutes',
        'is_flexible',
        'flexible_hours',
        'is_active',
    ];

    protected $casts = [
        'break_minutes' => 'integer',
        'is_flexible' => 'boolean',
        'flexible_hours' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'shift_id');
    }

    public function hasFixedTime(): bool
    {
        return !$this->is_flexible && $this->start_time && $this->end_time;
    }

    /**
     * True when the shift ends on the next calendar day, e.g. 22:00 - 08:00.
     */
    public function isOvernight(): bool
    {
        return $this->hasFixedTime() && substr($this->end_time, 0, 5) <= substr($this->start_time, 0, 5);
    }

    public function startsAt(Carbon $dutyDate): ?Carbon
    {
        if (!$this->hasFixedTime()) {
            return null;
        }

        return Carbon::parse($dutyDate->toDateString() . ' ' . $this->start_time, 'Asia/Dhaka');
    }

    public function endsAt(Carbon $dutyDate): ?Carbon
    {
        if (!$this->hasFixedTime()) {
            return null;
        }

        $end = Carbon::parse($dutyDate->toDateString() . ' ' . $this->end_time, 'Asia/Dhaka');

        return $this->isOvernight() ? $end->addDay() : $end;
    }

    /**
     * Paid working minutes expected for one duty (shift length minus unpaid break).
     */
    public function expectedMinutes(float $fallbackHours = 8): int
    {
        if ($this->is_flexible || !$this->hasFixedTime()) {
            $hours = $this->flexible_hours !== null ? (float) $this->flexible_hours : $fallbackHours;

            return (int) round($hours * 60);
        }

        $today = Carbon::now('Asia/Dhaka')->startOfDay();
        $length = $this->startsAt($today)->diffInMinutes($this->endsAt($today));

        return max(0, $length - (int) $this->break_minutes);
    }

    public function timeLabel(): string
    {
        if ($this->is_flexible || !$this->hasFixedTime()) {
            return 'Flexible' . ($this->flexible_hours ? ' · ' . rtrim(rtrim((string) $this->flexible_hours, '0'), '.') . 'h' : '');
        }

        $label = Carbon::parse($this->start_time)->format('h:i A') . ' – ' . Carbon::parse($this->end_time)->format('h:i A');

        return $this->isOvernight() ? $label . ' (+1)' : $label;
    }
}
