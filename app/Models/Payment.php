<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = ['branch_id','purchase_id', 'amount', 'payment_date', 'payment_method'];

    public function purchase() {
        return $this->belongsTo(Purchase::class);
    }
    public static $paymentStatusArray = ['Cash', 'Bank Transfer', 'Credit Card'];
}
