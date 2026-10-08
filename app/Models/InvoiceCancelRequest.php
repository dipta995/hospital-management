<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceCancelRequest extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'branch_id',
        'invoice_id',
        'invoice_number',
        'patient_name',
        'total_amount',
        'paid_amount',
        'requested_by',
        'reason',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_note',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }

    public function requester()
    {
        return $this->belongsTo(Admin::class, 'requested_by', 'id');
    }

    public function reviewer()
    {
        return $this->belongsTo(Admin::class, 'reviewed_by', 'id');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
}
