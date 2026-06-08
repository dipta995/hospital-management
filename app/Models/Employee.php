<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $casts = [
        'weekly_off_days' => 'array',
        'working_hours_per_day' => 'decimal:2',
        'annual_leave_quota' => 'integer',
    ];

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
