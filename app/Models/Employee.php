<?php

namespace App\Models;

use App\Services\HrSchemaService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'Active';
    public const STATUS_RESIGNED = 'Resigned';

    protected $casts = [
        'weekly_off_days' => 'array',
        'working_hours_per_day' => 'decimal:2',
        'annual_leave_quota' => 'integer',
        'resigned_at' => 'date',
    ];

    public function isResigned(): bool
    {
        return $this->status === self::STATUS_RESIGNED;
    }

    /**
     * Employees still on the payroll today.
     */
    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where(fn ($q) => $q->whereNull('status')->orWhere('status', '!=', self::STATUS_RESIGNED));
    }

    public function scopeResigned(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_RESIGNED);
    }

    /**
     * Employees who were still employed on $start or later.
     * The resign date is the last working day, so it still counts.
     */
    public function scopeEmployedSince(Builder $query, $start): Builder
    {
        $start = Carbon::parse($start)->toDateString();

        return $query->where(function ($q) use ($start) {
            $q->whereNull('status')->orWhere('status', '!=', self::STATUS_RESIGNED);
            if (HrSchemaService::hasResignColumns()) {
                $q->orWhere(fn ($r) => $r->where('status', self::STATUS_RESIGNED)->where('resigned_at', '>=', $start));
            }
        });
    }

    public function wasEmployedOn($date): bool
    {
        if (!$this->isResigned()) {
            return true;
        }

        return $this->resigned_at !== null
            && Carbon::parse($date)->startOfDay()->lte($this->resigned_at->copy()->startOfDay());
    }

    public function employeeSalaries()
    {
        return $this->hasMany(EmployeeSalary::class, 'employee_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
    // public function costs()
    // {
    //     return $this->hasMany(Cost::class, 'employee_id');
    // }
    public function costs()
    {
        return $this->hasMany(Cost::class, 'employee_id', 'id');
    }

    public function leaveDays(): HasMany
    {
        return $this->hasMany(EmployeeLeaveDay::class, 'employee_id', 'id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'employee_id', 'id');
    }

    public function shift()
    {
        return $this->belongsTo(AttendanceShift::class, 'shift_id');
    }

    // // Virtual attribute to calculate remaining salary
    // public function getNetSalaryAttribute()
    // {
    //     $totalCosts = $this->costs()->sum('amount');
    //     return $this->salary - $totalCosts;
    // }
    public function getTotalCostsAttribute()
    {
        return $this->costs()->sum('amount');
    }

    public function getNetSalaryAttribute()
    {
        $totalCosts = $this->costs()->sum('amount');
        return $this->salary - $totalCosts;
    }
    public function getAfterCost($id)
{
    $employee = Employee::with('employeeSalaries')->findOrFail($id);

    $totalCost = $employee->employeeSalaries->sum('salary');
    $afterCost = $employee->salary - $totalCost;

    return response()->json([
        'after_cost' => $afterCost,
    ]);
}





}
