<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public function isEnabled(): bool
    {
        return (bool) config('sms.enabled');
    }

    public function driver(): string
    {
        return (string) config('sms.driver', 'log');
    }

    /**
     * @return string[]
     */
    public function adminNumbers(): array
    {
        return array_map([$this, 'normalizeNumber'], config('sms.admin_numbers', []));
    }

    /**
     * @return array{ok:bool, message:string}
     */
    public function sendToAdmins(string $message): array
    {
        $numbers = $this->adminNumbers();

        if (empty($numbers)) {
            return ['ok' => false, 'message' => 'config/sms.php-তে admin_numbers সেট করা নেই।'];
        }

        return $this->send($numbers, $message);
    }

    /**
     * Never throws: SMS failure must not break the calling flow.
     *
     * @param string[] $numbers
     * @return array{ok:bool, message:string}
     */
    public function send(array $numbers, string $message): array
    {
        if (!$this->isEnabled()) {
            return ['ok' => false, 'message' => 'SMS বন্ধ আছে (config/sms.php: enabled = false)।'];
        }

        $numbers = array_values(array_filter(array_map([$this, 'normalizeNumber'], $numbers)));
        if (empty($numbers)) {
            return ['ok' => false, 'message' => 'কোনো বৈধ নম্বর নেই।'];
        }

        try {
            $result = match ($this->driver()) {
                'bdbulksms' => $this->sendViaBdBulkSms($numbers, $message),
                'bulksmsbd' => $this->sendViaBulkSmsBd($numbers, $message),
                'custom' => $this->sendViaCustomUrl($numbers, $message),
                default => $this->sendViaLog($numbers, $message),
            };
        } catch (\Throwable $e) {
            $result = ['ok' => false, 'message' => $e->getMessage()];
        }

        if (!$result['ok']) {
            Log::warning('SMS send failed', [
                'driver' => $this->driver(),
                'numbers' => $numbers,
                'error' => $result['message'],
            ]);
        }

        return $result;
    }

    /**
     * IPv4 only: some hosts have broken IPv6 routes, which makes cURL hang until the connect timeout.
     */
    protected function http(): PendingRequest
    {
        return Http::timeout((int) config('sms.timeout', 15))
            ->connectTimeout(5)
            ->withOptions(['force_ip_resolve' => 'v4']);
    }

    protected function sendViaBdBulkSms(array $numbers, string $message): array
    {
        $config = config('sms.drivers.bdbulksms');

        if (blank($config['token'] ?? null)) {
            return ['ok' => false, 'message' => 'config/sms.php-তে bdbulksms token সেট করা নেই।'];
        }

        $response = $this->http()
            ->acceptJson()
            ->withOptions(['verify' => (bool) ($config['verify_ssl'] ?? true)])
            ->withBody(json_encode([
                'token' => $config['token'],
                'smsdata' => array_map(fn ($number) => ['to' => '+' . $number, 'message' => $message], $numbers),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'application/json')
            ->post($config['url']);

        $data = $response->json();
        $results = is_array($data) && array_is_list($data) ? $data : [];
        $sent = array_filter($results, fn ($row) => strtoupper((string) ($row['status'] ?? '')) === 'SENT');

        if ($results && count($sent) === count($results)) {
            return ['ok' => true, 'message' => 'SMS পাঠানো হয়েছে।'];
        }

        $error = collect($results)->pluck('statusmsg')->filter()->implode(', ')
            ?: (is_array($data) ? ($data['error'] ?? $data['message'] ?? null) : null)
            ?: $response->body();

        return ['ok' => false, 'message' => 'bdbulksms error (HTTP ' . $response->status() . '): ' . $error];
    }

    protected function sendViaBulkSmsBd(array $numbers, string $message): array
    {
        $config = config('sms.drivers.bulksmsbd');

        if (blank($config['api_key'] ?? null) || blank($config['sender_id'] ?? null)) {
            return ['ok' => false, 'message' => 'config/sms.php-তে bulksmsbd api_key / sender_id সেট করা নেই।'];
        }

        $response = $this->http()
            ->asForm()
            ->post($config['url'], [
                'api_key' => $config['api_key'],
                'type' => 'text',
                'number' => implode(',', $numbers),
                'senderid' => $config['sender_id'],
                'message' => $message,
            ]);

        $data = $response->json() ?? [];
        $code = (int) ($data['response_code'] ?? 0);

        return $code === 202
            ? ['ok' => true, 'message' => 'SMS পাঠানো হয়েছে।']
            : ['ok' => false, 'message' => 'BulkSMSBD error ' . ($code ?: $response->status()) . ': ' . ($data['error_message'] ?? $data['success_message'] ?? $response->body())];
    }

    protected function sendViaCustomUrl(array $numbers, string $message): array
    {
        $template = (string) config('sms.drivers.custom.url');

        if ($template === '') {
            return ['ok' => false, 'message' => 'config/sms.php-তে custom url সেট করা নেই।'];
        }

        foreach ($numbers as $number) {
            $url = str_replace(
                ['{number}', '{message}'],
                [rawurlencode($number), rawurlencode($message)],
                $template
            );

            $response = $this->http()->get($url);
            if (!$response->successful()) {
                return ['ok' => false, 'message' => 'Custom SMS HTTP ' . $response->status() . ': ' . $response->body()];
            }
        }

        return ['ok' => true, 'message' => 'SMS পাঠানো হয়েছে।'];
    }

    protected function sendViaLog(array $numbers, string $message): array
    {
        Log::info('SMS (log driver)', ['numbers' => $numbers, 'message' => $message]);

        return ['ok' => true, 'message' => 'SMS log-এ লেখা হয়েছে (driver = log)।'];
    }

    protected function normalizeNumber(string $number): string
    {
        $digits = preg_replace('/\D+/', '', $number);

        if (str_starts_with($digits, '01') && strlen($digits) === 11) {
            return '88' . $digits;
        }

        return $digits;
    }
}
