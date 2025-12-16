<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cost extends Model
{
    use HasFactory;

    public static $paymentArray = ['Cash','Bkash','Nagad','Bank'];
      protected $fillable = [
        'branch_id',
        'invoice_id',
        'cost_category_id',
        'refer_id',
        'admin_id',
        'employee_id',
        'reason',
        'amount',
        'account_details',
        'account_type',
        'payment_type',
        'creation_date',
    ];

    public function category()
    {
        return $this->belongsTo(CostCategory::class, 'cost_category_id', 'id');
    } public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }     public function costCategory()
    {
        return $this->belongsTo(CostCategory::class, 'cost_category_id', 'id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function reeferBy()
    {
        return $this->belongsTo(Reefer::class, 'refer_id', 'id');
    }
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
    protected static function booted()
    {
        // When a cost is created, add its amount to the employee’s total_costs
        static::created(function ($cost) {
            if ($cost->employee_id && $cost->employee) {
                $cost->employee->increment('total_costs', $cost->amount);
            }
        });

        // When a cost is deleted, subtract its amount from the employee’s total_costs
        static::deleting(function ($cost) {
            if ($cost->employee_id && $cost->employee) {
                $cost->employee->decrement('total_costs', $cost->amount);
            }
        });
    }

}
