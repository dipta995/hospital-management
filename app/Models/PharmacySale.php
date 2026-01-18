<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Reefer;

class PharmacySale extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'customer_id',
        'dr_refer_id',
        'sale_date',
        'total_amount',
        'discount_amount',
        'paid_amount',
        'due_amount',
        'payment_method',
        'note',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(PharmacySaleItem::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Reefer::class, 'dr_refer_id');
    }
}
