<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendence extends Model
{
    use HasFactory;

    protected $table = 'attendence';
    protected $fillable = [
        'branch_id',
        'employee_id',
        'date',
        'time'
    ];
    public function emoloyee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
