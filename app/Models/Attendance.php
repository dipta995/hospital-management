<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'fingerprint_data',
        'mode',
        'hour_slot',
        'date',
        'in_time',
        'out_time',
        'note',
        'shift_id',
        'source',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function shift()
    {
        return $this->belongsTo(AttendanceShift::class, 'shift_id');
    }
}
