<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeLeaveDay extends Model
{
    public const TYPES = [
        'leave' => 'General Leave',
        'sick_leave' => 'Sick Leave',
        'casual_leave' => 'Casual Leave',
        'emergency_leave' => 'Emergency Leave',
        'unpaid_leave' => 'Unpaid Leave',
        'other' => 'Other',
    ];

    protected $fillable = [
        'employee_id',
        'branch_id',
        'date',
        'type',
        'reason',
        'is_paid',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'is_paid' => 'boolean',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function createdByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? ucfirst(str_replace('_', ' ', $this->type));
    }
}
