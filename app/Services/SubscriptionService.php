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
                'is_last_day' => false,
                'popup_interval_minutes' => 0,
                'popup_normal_interval_minutes' => 0,
                'popup_last_day_interval_minutes' => 0,
                'last_day_starts_at' => null,
                'popup_close_delay' => 0,
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
        $startDate = Carbon::parse(Carbon::parse($subscription->start_date)->toDateString(), 'Asia/Dhaka')->startOfDay();
        $endDate = Carbon::parse(Carbon::parse($subscription->end_date)->toDateString(), 'Asia/Dhaka')->endOfDay();

        $daysUsed = 0;
        if ($now->greaterThanOrEqualTo($startDate)) {
            $daysUsed = min(30, (int) round($startDate->diffInDays($now->copy()->startOfDay())) + 1);
        }

        $daysLeft = 0;
        if ($now->lessThanOrEqualTo($endDate)) {
            $daysLeft = max(0, (int) round($now->copy()->startOfDay()->diffInDays($endDate->copy()->startOfDay())) + 1);
        }

        $expired = $now->greaterThan($endDate);

        $reminder = config('subscription.reminder');
        $isLastDay = !$expired && $daysLeft <= 1;
        $normalIntervalMinutes = max(1, (int) round($reminder['interval_hours'] * 60));
        $lastDayIntervalMinutes = max(1, (int) round($reminder['last_day_interval_hours'] * 60));

        return [
            'has_subscription' => true,
            'subscription_id' => $subscription->id,
            'expired' => $expired,
            'show_popup' => !$expired && $daysLeft <= $reminder['days_before'],
            'is_last_day' => $isLastDay,
            'popup_interval_minutes' => $isLastDay ? $lastDayIntervalMinutes : $normalIntervalMinutes,
            'popup_normal_interval_minutes' => $normalIntervalMinutes,
            'popup_last_day_interval_minutes' => $lastDayIntervalMinutes,
            'last_day_starts_at' => $endDate->copy()->startOfDay()->getTimestampMs(),
            'popup_close_delay' => max(0, $reminder['close_delay_seconds']),
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
