<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPaymentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'admin_id',
        'transaction_id',
        'transaction_date',
        'amount',
        'sender_number',
        'note',
        'gateway',
        'invoice_number',
        'payment_method',
        'gateway_response',
        'payer_name',
        'payer_email',
        'invoice_no',
        'invoiced_at',
        'sms_sent_at',
        'status',
        'approved_by',
        'approved_at',
        'reject_reason',
        'submitted_at',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'approved_at' => 'datetime',
        'submitted_at' => 'datetime',
        'gateway_response' => 'array',
        'invoiced_at' => 'datetime',
        'sms_sent_at' => 'datetime',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function approver()
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }

    public function submittedByAdmin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
