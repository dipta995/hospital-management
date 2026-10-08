<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Subscription;
use App\Models\SubscriptionPaymentRequest;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SubscriptionInvoiceService
{
    public function __construct(
        protected SmsService $sms
    ) {
    }

    public function assign(SubscriptionPaymentRequest $paymentRequest): SubscriptionPaymentRequest
    {
        if ($paymentRequest->status !== 'approved' || filled($paymentRequest->invoice_no)) {
            return $paymentRequest;
        }

        if (!app(PaymentGatewayMigrationService::class)->isInstalled()) {
            return $paymentRequest;
        }

        $paymentRequest->forceFill([
            'invoice_no' => 'SINV-' . str_pad((string) $paymentRequest->id, 6, '0', STR_PAD_LEFT),
            'invoiced_at' => Carbon::now('Asia/Dhaka'),
        ])->save();

        return $paymentRequest;
    }

    /**
     * Same rule as approval: subscription runs one month from the payment date.
     *
     * @return array{0:Carbon, 1:Carbon}
     */
    public function period(SubscriptionPaymentRequest $paymentRequest): array
    {
        $start = Carbon::parse(
            $paymentRequest->transaction_date ?? $paymentRequest->approved_at ?? now(),
            'Asia/Dhaka'
        )->startOfDay();

        return [$start, $start->copy()->addMonthNoOverflow()];
    }

    public function url(SubscriptionPaymentRequest $paymentRequest): string
    {
        return route('subscription.invoice', [
            'token' => $paymentRequest->subscription->public_token,
            'requestRow' => $paymentRequest->id,
        ]);
    }

    /**
     * Settings are stored per branch; public pages and gateway callbacks have no logged-in admin,
     * so resolve the branch from the subscription first.
     */
    public function setting(?Subscription $subscription, string $key): ?string
    {
        $branchId = $subscription?->branch_id ?? auth('admin')->user()?->branch_id;

        return $branchId ? Setting::getByBranch($branchId, $key) : null;
    }

    /**
     * Sends the payment alert once; safe to call again on callback/IPN retries.
     */
    public function notifyAdminsBySms(SubscriptionPaymentRequest $paymentRequest): void
    {
        if (filled($paymentRequest->sms_sent_at) || !$this->sms->isEnabled()) {
            return;
        }

        $result = $this->sms->sendToAdmins($this->smsText($paymentRequest));

        if ($result['ok']) {
            $paymentRequest->forceFill(['sms_sent_at' => Carbon::now('Asia/Dhaka')])->save();
        }
    }

    /**
     * ASCII only so the alert fits in a single 160-character SMS.
     */
    public function smsText(SubscriptionPaymentRequest $paymentRequest): string
    {
        [, $end] = $this->period($paymentRequest);

        $parts = [
            'Paid BDT ' . number_format((float) $paymentRequest->amount, 2, '.', ''),
            $paymentRequest->invoice_no ?: $paymentRequest->invoice_number,
            trim(($paymentRequest->payment_method ?: 'Online') . ' ' . $paymentRequest->transaction_id),
            'Valid till ' . $end->format('d M Y'),
        ];

        if (filled($paymentRequest->sender_number)) {
            $parts[] = 'From ' . $paymentRequest->sender_number;
        }

        $company = (string) ($this->setting($paymentRequest->subscription, 'company_name') ?: config('app.name'));
        if ($company !== '' && mb_check_encoding($company, 'ASCII')) {
            $parts[] = $company;
        }

        return Str::limit(implode(' | ', $parts), 160, '');
    }
}
