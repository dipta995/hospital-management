<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'start_date',
        'end_date',
        'payment_amount',
        'public_token',
        'payment_rules',
        'transaction_details_note',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'payment_amount' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Subscription $subscription) {
            if (empty($subscription->public_token)) {
                $subscription->public_token = (string) Str::uuid();
            }
        });
    }

    public function paymentRequests()
    {
        return $this->hasMany(SubscriptionPaymentRequest::class);
    }

    public function renewFromDate(Carbon $date): void
    {
        $startDate = $date->copy()->startOfDay();

        $this->start_date = $startDate->toDateString();
        $this->end_date = $startDate->copy()->addDays(29)->toDateString();
    }
}
