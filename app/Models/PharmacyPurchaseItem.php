<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyPurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'pharmacy_product_id',
        'supplier_id',
        'pharmacy_purchase_id',
        'quantity',
        'quantity_spend',
        'unit_price',
        'discount_amount',
        'total_amount',
        'expiry_date',
    ];

    public function purchase()
    {
        return $this->belongsTo(PharmacyPurchase::class, 'pharmacy_purchase_id');
    }

    public function product()
    {
        return $this->belongsTo(PharmacyProduct::class, 'pharmacy_product_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
