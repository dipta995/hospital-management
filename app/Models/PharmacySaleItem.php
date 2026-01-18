<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacySaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'pharmacy_sale_id',
        'pharmacy_product_id',
        'quantity',
        'unit_price',
        'discount_amount',
        'total_amount',
    ];

    public function sale()
    {
        return $this->belongsTo(PharmacySale::class, 'pharmacy_sale_id');
    }

    public function product()
    {
        return $this->belongsTo(PharmacyProduct::class, 'pharmacy_product_id');
    }
}
