<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\SubscriptionPaymentRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayStationPaymentFinalizer
{
    public const GATEWAY = 'paystation';

    /** PayStation can report "processing" for a few seconds after a successful checkout. */
    protected const SUCCESS_RECHECK_ATTEMPTS = 4;
    protected const SUCCESS_RECHECK_DELAY_SECONDS = 2;

    protected const ABANDON_AFTER_HOURS = 24;

    public function __construct(
        protected PayStationService $payStation,
        protected SubscriptionInvoiceService $invoices
    ) {
    }

    /**
     * @return array{ok:bool, retry:bool, message:string}
     */
    public function finalize(SubscriptionPaymentRequest $paymentRequest, string $callbackStatus = ''): array
    {
        if ($paymentRequest->status === 'approved') {
            $this->invoices->assign($paymentRequest);
            $this->invoices->notifyAdminsBySms($paymentRequest);

            return ['ok' => true, 'retry' => false, 'message' => 'পেমেন্ট ইতোমধ্যে সফল হয়েছে এবং সাবস্ক্রিপশন নবায়ন হয়েছে।'];
        }

        if ($this->belongsToOtherMode($paymentRequest)) {
            $paymentRequest->update([
                'status' => 'failed',
                'reject_reason' => $this->payStation->isSandbox()
                    ? 'Live পেমেন্ট, sandbox mode থেকে যাচাই করা যায় না'
                    : 'Sandbox টেস্ট পেমেন্ট (আসল টাকা নয়)',
            ]);

            return ['ok' => false, 'retry' => false, 'message' => 'এটি ভিন্ন mode-এর (sandbox/live) পেমেন্ট, তাই যাচাই করা যায়নি।'];
        }

        $callbackStatus = strtolower(trim($callbackStatus));
        $callbackSaysSuccess = in_array($callbackStatus, ['successful', 'success', 'completed'], true);
        $attempts = $callbackSaysSuccess ? self::SUCCESS_RECHECK_ATTEMPTS : 1;
        $status = [];

        for ($i = 1; $i <= $attempts; $i++) {
            try {
                $status = $this->payStation->transactionStatus($paymentRequest->invoice_number);
            } catch (\Throwable $e) {
                Log::warning('PayStation status check failed', [
                    'invoice_number' => $paymentRequest->invoice_number,
                    'error' => $e->getMessage(),
                ]);

                return ['ok' => false, 'retry' => true, 'message' => 'PayStation থেকে পেমেন্ট যাচাই করা যায়নি। কিছুক্ষণ পরে আবার চেষ্টা করুন।'];
            }

            if ($status['paid'] || $status['status'] !== 'processing' || $i === $attempts) {
                break;
            }

            sleep(self::SUCCESS_RECHECK_DELAY_SECONDS);
        }

        if ($status['paid']) {
            if ($status['amount'] + 0.01 < (float) $paymentRequest->amount) {
                $paymentRequest->update([
                    'status' => 'pending',
                    'transaction_id' => $status['trx_id'] ?? $paymentRequest->invoice_number,
                    'payment_method' => $status['payment_method'],
                    'gateway_response' => $status['response'],
                ]);

                return ['ok' => false, 'retry' => false, 'message' => 'পেমেন্টের পরিমাণ মিলছে না। অ্যাডমিন যাচাই করে অনুমোদন করবেন।'];
            }

            $this->approve($paymentRequest, $status);
            $this->invoices->notifyAdminsBySms($paymentRequest);

            return ['ok' => true, 'retry' => false, 'message' => 'PayStation পেমেন্ট সফল হয়েছে। সাবস্ক্রিপশনের মেয়াদ নবায়ন হয়েছে। Invoice: ' . $paymentRequest->invoice_no];
        }

        $cancelledByUser = in_array($callbackStatus, ['failed', 'canceled', 'cancelled'], true);
        $abandoned = $paymentRequest->created_at
            && $paymentRequest->created_at->lt(now()->subHours(self::ABANDON_AFTER_HOURS));

        if (in_array($status['status'], ['failed', 'cancelled', 'refund'], true) || $cancelledByUser || $abandoned) {
            $paymentRequest->update([
                'status' => 'failed',
                'gateway_response' => $status['response'],
                'reject_reason' => 'PayStation: ' . ($abandoned && !$cancelledByUser && $status['status'] === 'processing'
                    ? 'পেমেন্ট সম্পন্ন করা হয়নি'
                    : ($status['status'] ?: $callbackStatus)),
            ]);

            return ['ok' => false, 'retry' => false, 'message' => 'পেমেন্ট সম্পন্ন হয়নি বা বাতিল করা হয়েছে।'];
        }

        $paymentRequest->update(['gateway_response' => $status['response']]);

        return ['ok' => false, 'retry' => false, 'message' => 'PayStation এখনও পেমেন্টটি সম্পন্ন হিসেবে দেখাচ্ছে না (processing)। bKash/Nagad-এ টাকা কেটে থাকলে কিছুক্ষণ পর সাবস্ক্রিপশন পেজে "যাচাই করুন" চাপুন।'];
    }

    /**
     * Re-check recent unfinished PayStation payments (covers missed callback/IPN, e.g. on localhost).
     */
    public function reconcile(int $subscriptionId, bool $throttle = true): void
    {
        if (!$this->payStation->isEnabled()) {
            return;
        }

        if ($throttle && !Cache::add('paystation_reconcile_' . $subscriptionId, 1, now()->addMinute())) {
            return;
        }

        SubscriptionPaymentRequest::with('subscription')
            ->where('subscription_id', $subscriptionId)
            ->where('gateway', self::GATEWAY)
            ->where('status', 'initiated')
            ->where('created_at', '>=', now()->subDays(3))
            ->latest('id')
            ->take(5)
            ->get()
            ->each(function (SubscriptionPaymentRequest $row) {
                try {
                    $this->finalize($row);
                } catch (\Throwable $e) {
                    report($e);
                }
            });

        SubscriptionPaymentRequest::where('subscription_id', $subscriptionId)
            ->where('gateway', self::GATEWAY)
            ->where('status', 'approved')
            ->whereNull('sms_sent_at')
            ->where('approved_at', '>=', now()->subDays(2))
            ->latest('id')
            ->take(3)
            ->get()
            ->each(fn (SubscriptionPaymentRequest $row) => $this->invoices->notifyAdminsBySms($row));
    }

    /**
     * Sandbox and live are separate PayStation systems; an invoice from one is unknown to the other.
     */
    protected function belongsToOtherMode(SubscriptionPaymentRequest $paymentRequest): bool
    {
        $createdInSandbox = str_contains((string) $paymentRequest->note, '(Sandbox)');

        return $createdInSandbox !== $this->payStation->isSandbox();
    }

    protected function approve(SubscriptionPaymentRequest $paymentRequest, array $status): void
    {
        DB::transaction(function () use ($paymentRequest, $status) {
            $row = SubscriptionPaymentRequest::whereKey($paymentRequest->id)->lockForUpdate()->first();
            if (!$row || $row->status === 'approved') {
                return;
            }

            $paidOn = Carbon::now('Asia/Dhaka')->startOfDay();

            $payerMobile = trim((string) ($status['response']['data']['payer_mobile_no'] ?? ''));

            $row->update([
                'status' => 'approved',
                'sender_number' => $payerMobile !== '' ? $payerMobile : $row->sender_number,
                'transaction_id' => $status['trx_id'] ?? $row->invoice_number,
                'transaction_date' => $paidOn->toDateString(),
                'payment_method' => $status['payment_method'],
                'gateway_response' => $status['response'],
                'approved_at' => Carbon::now('Asia/Dhaka'),
                'reject_reason' => null,
            ]);

            $subscription = Subscription::whereKey($row->subscription_id)->lockForUpdate()->first();
            $subscription->start_date = $paidOn->toDateString();
            $subscription->end_date = $paidOn->copy()->addMonthNoOverflow()->toDateString();
            $subscription->save();

            $this->invoices->assign($row);
        });

        $paymentRequest->refresh();
    }
}
