<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'brand_id',
        'type_id',
        'quantity_type_id',
        'name',
        'generic_name',
        'strength',
        'barcode',
        'purchase_price',
        'sell_price',
        'alert_qty',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(PharmacyCategory::class, 'category_id', 'id');
    }

    public function brand()
    {
        return $this->belongsTo(PharmacyBrand::class, 'brand_id', 'id');
    }

    public function type()
    {
        return $this->belongsTo(PharmacyType::class, 'type_id', 'id');
    }

    public function quantityType()
    {
        return $this->belongsTo(PharmacyUnit::class, 'quantity_type_id', 'id');
    }
}
