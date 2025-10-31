<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;
    protected $fillable = [
        'branch_id','supplier_id', 'purchase_date', 'total_cost', 'paid_amount', 'due_amount', 'notes'
    ];

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }
    public function purchaseItems() {
        return $this->hasMany(PurchaseItem::class);
    }    public function purchasePaid() {
        return $this->hasMany(Payment::class);
    }
    public function item() {
        return $this->belongsTo(Item::class);
    }
    public static $purchaseStatusArray = ['Pending', 'Partially Paid', 'Paid'];
}
