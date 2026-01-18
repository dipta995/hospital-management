<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'supplier_id',
        'purchase_date',
        'total_cost',
        'paid_amount',
        'due_amount',
        'notes',
        'status',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PharmacyPurchaseItem::class);
    }

    public static $statusArray = ['Pending', 'Partially Paid', 'Paid'];
}
