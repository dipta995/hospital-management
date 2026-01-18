<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacySalePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'pharmacy_sale_id',
        'branch_id',
        'admin_id',
        'paid_amount',
        'creation_date',
    ];

    public function sale()
    {
        return $this->belongsTo(PharmacySale::class, 'pharmacy_sale_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
