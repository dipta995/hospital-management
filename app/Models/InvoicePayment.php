<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model
{

    protected $table = 'invoice_payments';
    protected $fillable = [
        'invoice_id',
        'branch_id',
        'admin_id',
        'paid_amount',
        'creation_date',
    ];
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }


}
