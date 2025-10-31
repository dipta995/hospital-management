<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'item_id',
        'quantity_spend',
        'supplier_id',
        'purchase_id',
        'quantity',
        'unit_price',
        'discount_amount',
        'total_amount',
        'expiry_date'

    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }
}
