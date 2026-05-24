<?php

namespace App\Services;

use App\Models\Subscription;
use Carbon\Carbon;

class SubscriptionService
{
    public static function getCurrentForBranch(?int $branchId): ?Subscription
    {
        if ($branchId) {
            $branchSubscription = Subscription::where('branch_id', $branchId)
                ->latest('id')
                ->first();

            if ($branchSubscription) {
                return $branchSubscription;
            }
        }

        return Subscription::whereNull('branch_id')->latest('id')->first();
    }

    public static function getMetaForBranch(?int $branchId): array
    {
        $subscription = self::getCurrentForBranch($branchId);

        if (!$subscription) {
            return [
                'has_subscription' => false,
                'subscription_id' => null,
                'expired' => true,
                'show_popup' => false,
                'show_banner' => false,
                'start_date' => null,
                'start_date_pretty' => null,
                'end_date' => null,
                'end_date_pretty' => null,
                'days_used' => 0,
                'days_left' => 0,
                'payment_amount' => null,
                'payment_rules' => null,
                'transaction_details_note' => null,
                'payment_url' => null,
                'payment_list_url' => null,
            ];
        }

        $now = Carbon::now('Asia/Dhaka');
        $startDate = Carbon::parse($subscription->start_date)->startOfDay();
        $endDate = Carbon::parse($subscription->end_date)->endOfDay();

        $daysUsed = 0;
        if ($now->greaterThanOrEqualTo($startDate)) {
            $daysUsed = min(30, $startDate->diffInDays($now->copy()->startOfDay()) + 1);
        }

        $daysLeft = 0;
        if ($now->lessThanOrEqualTo($endDate)) {
            $daysLeft = max(0, $now->copy()->startOfDay()->diffInDays($endDate->copy()->startOfDay()) + 1);
        }

        $expired = $now->greaterThan($endDate);

        return [
            'has_subscription' => true,
            'subscription_id' => $subscription->id,
            'expired' => $expired,
            'show_popup' => !$expired && $daysUsed >= 20,
            'show_banner' => !$expired && $daysUsed >= 28,
            'start_date' => $startDate->toDateString(),
            'start_date_pretty' => $startDate->format('jS F Y'),
            'end_date' => $endDate->toDateString(),
            'end_date_pretty' => $endDate->format('jS F Y'),
            'days_used' => $daysUsed,
            'days_left' => $daysLeft,
            'payment_amount' => $subscription->payment_amount,
            'payment_rules' => $subscription->payment_rules,
            'transaction_details_note' => $subscription->transaction_details_note,
            'payment_url' => route('subscription.payment.public', $subscription->public_token),
            'payment_list_url' => route('subscription.payment.list.public', $subscription->public_token),
        ];
    }
}
