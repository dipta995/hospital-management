<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class PayStationService
{
    public function isEnabled(): bool
    {
        return (bool) config('paystation.enabled')
            && filled(config('paystation.merchant_id'))
            && filled(config('paystation.password'))
            && app(PaymentGatewayMigrationService::class)->isInstalled();
    }

    public function canPay(?\App\Models\Subscription $subscription): bool
    {
        return $subscription
            && (float) ($subscription->payment_amount ?? 0) > 0
            && $this->isEnabled();
    }

    public function isSandbox(): bool
    {
        return config('paystation.mode') !== 'live';
    }

    public function baseUrl(): string
    {
        $mode = $this->isSandbox() ? 'sandbox' : 'live';

        return rtrim((string) config("paystation.base_urls.{$mode}"), '/');
    }

    /**
     * @param array{invoice_number:string, amount:float, cust_name:string, cust_phone:string, cust_email:string, callback_url:string, reference?:string, checkout_items?:string, cust_address?:string} $payload
     * @return array{payment_url:string, response:array}
     */
    public function initiatePayment(array $payload): array
    {
        $response = Http::asForm()
            ->acceptJson()
            ->timeout((int) config('paystation.timeout', 30))
            ->connectTimeout(10)
            ->withOptions(['force_ip_resolve' => 'v4'])
            ->post($this->baseUrl() . '/initiate-payment', [
                'merchantId' => config('paystation.merchant_id'),
                'password' => config('paystation.password'),
                'invoice_number' => $payload['invoice_number'],
                'currency' => config('paystation.currency', 'BDT'),
                'payment_amount' => $this->formatAmount($payload['amount']),
                'pay_with_charge' => (int) config('paystation.pay_with_charge', 0),
                'reference' => $payload['reference'] ?? '',
                'cust_name' => $payload['cust_name'],
                'cust_phone' => $payload['cust_phone'],
                'cust_email' => $payload['cust_email'],
                'cust_address' => $payload['cust_address'] ?? '',
                'callback_url' => $payload['callback_url'],
                'checkout_items' => $payload['checkout_items'] ?? '',
            ]);

        $data = $response->json() ?? [];

        if ((string) ($data['status_code'] ?? '') !== '200' || empty($data['payment_url'])) {
            throw new RuntimeException($data['message'] ?? ('PayStation request failed (HTTP ' . $response->status() . ').'));
        }

        return [
            'payment_url' => $data['payment_url'],
            'response' => $data,
        ];
    }

    /**
     * Server-to-server status check by our invoice number. Never trust callback/IPN params alone.
     *
     * @return array{paid:bool, status:string, trx_id:?string, amount:float, payment_method:?string, response:array}
     */
    public function transactionStatus(string $invoiceNumber): array
    {
        $response = Http::asForm()
            ->acceptJson()
            ->timeout((int) config('paystation.timeout', 30))
            ->connectTimeout(10)
            ->withOptions(['force_ip_resolve' => 'v4'])
            ->withHeaders(['merchantId' => config('paystation.merchant_id')])
            ->post($this->baseUrl() . '/transaction-status', [
                'invoice_number' => $invoiceNumber,
            ]);

        $data = $response->json() ?? [];

        if ((string) ($data['status_code'] ?? '') !== '200') {
            throw new RuntimeException($data['message'] ?? ('PayStation status check failed (HTTP ' . $response->status() . ').'));
        }

        $trx = $data['data'] ?? [];
        $status = $this->normalizeStatus((string) ($trx['trx_status'] ?? ''));

        return [
            'paid' => $status === 'success',
            'status' => $status,
            'trx_id' => filled($trx['trx_id'] ?? null) ? (string) $trx['trx_id'] : null,
            'amount' => (float) ($trx['payment_amount'] ?? $trx['trx_amount'] ?? 0),
            'payment_method' => filled($trx['payment_method'] ?? null) ? (string) $trx['payment_method'] : null,
            'response' => $data,
        ];
    }

    /**
     * Live reports "successful"/"cancelled", sandbox has reported "success"; collapse the variants.
     */
    protected function normalizeStatus(string $status): string
    {
        $status = strtolower(trim($status));

        return match ($status) {
            'success', 'successful', 'completed', 'complete', 'paid' => 'success',
            'failed', 'failure', 'fail' => 'failed',
            'cancelled', 'canceled', 'cancel' => 'cancelled',
            'refund', 'refunded' => 'refund',
            'processing', 'pending', 'initiated', '' => 'processing',
            default => $status,
        };
    }

    protected function formatAmount(float $amount): string
    {
        $amount = round($amount, 2);

        return floor($amount) == $amount
            ? (string) (int) $amount
            : number_format($amount, 2, '.', '');
    }
}
