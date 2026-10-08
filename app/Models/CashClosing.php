<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashClosing extends Model
{
    public const STATUS_CLOSED = 'closed';
    public const STATUS_VERIFIED = 'verified';

    protected $fillable = [
        'branch_id',
        'admin_id',
        'closing_date',
        'payment_count',
        'system_amount',
        'counted_amount',
        'difference',
        'breakdown',
        'note',
        'status',
        'verified_by',
        'verified_at',
        'verify_note',
    ];

    protected $casts = [
        'closing_date' => 'date',
        'system_amount' => 'decimal:2',
        'counted_amount' => 'decimal:2',
        'difference' => 'decimal:2',
        'breakdown' => 'array',
        'verified_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    public function verifier()
    {
        return $this->belongsTo(Admin::class, 'verified_by', 'id');
    }
}
