<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';
    protected $fillable = [
        'branch_id',
        'admin_id',
        'dr_refer_id',
        'refer_id',
        'invoice_number',
        'total_amount',
        'discount_amount',
        'refer_fee_total',
        'refer_fee_total_agent',
//        'discount_percent',
        'delivery_at',
        'payment_type',
        'patient_name',
        'patient_age_year',
        'patient_phone',
        'patient_email',
        'patient_gender',
        'patient_blood_group',
        'patient_address',
        'note',
        'creation_date',
        'discount_by',
        'dr_name',
    ];

    public static $paymentArray = ['Cash','Bkash','Nagad','Bank'];
    public static $deliveryStatusArray = ['Pending','Complete'];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }
    public function reeferDr()
    {
        return $this->belongsTo(Reefer::class, 'dr_refer_id', 'id');
    }
    public function reeferBy()
    {
        return $this->belongsTo(Reefer::class, 'refer_id', 'id');
    }
    public function costs()
    {
        return $this->hasMany(Cost::class, 'invoice_id', 'id');
    }

    public function invoiceList()
    {
        return $this->hasMany(InvoiceList::class, 'invoice_id', 'id');
    }
    public function tests()
    {
        return $this->hasMany(InvoiceList::class);
    }

    public function isFullyProcessed()
    {
        return !$this->invoiceList()
            ->whereIn('status', ['Pending', 'Processing', 'Rejected'])
            ->exists();    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function paidAmount()
    {
        return $this->hasMany(InvoicePayment::class, 'invoice_id', 'id');
    }

}
