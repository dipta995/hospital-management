<?php

namespace App\Http\Controllers;

use App\Helper\RedirectHelper;
use App\Models\Subscription;
use App\Models\SubscriptionPaymentRequest;
use App\Services\PayStationPaymentFinalizer;
use App\Services\PayStationService;
use App\Services\SubscriptionInvoiceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SubscriptionPayStationController extends Controller
{
    public const GATEWAY = 'paystation';

    public function __construct(
        protected PayStationService $payStation,
        protected SubscriptionInvoiceService $invoices,
        protected PayStationPaymentFinalizer $finalizer
    ) {
    }

    public function initiate(Request $request, string $token): RedirectResponse
    {
        abort_unless($this->payStation->isEnabled(), 404);

        $subscription = Subscription::where('public_token', $token)->firstOrFail();
        $amount = round((float) ($subscription->payment_amount ?? 0), 2);

        if ($amount <= 0) {
            return back()->withErrors(['paystation' => 'নির্ধারিত পেমেন্টের পরিমাণ সেট করা নেই।']);
        }

        $data = $request->validate([
            'cust_name' => 'required|string|max:100',
            'cust_phone' => 'required|string|max:20',
            'cust_email' => 'required|email|max:150',
        ]);

        $invoiceNumber = 'SUB' . $subscription->id . now('Asia/Dhaka')->format('ymdHis') . Str::upper(Str::random(4));

        $paymentRequest = SubscriptionPaymentRequest::create([
            'subscription_id' => $subscription->id,
            'admin_id' => auth('admin')->id(),
            'transaction_id' => $invoiceNumber,
            'transaction_date' => now('Asia/Dhaka')->toDateString(),
            'amount' => $amount,
            'sender_number' => $data['cust_phone'],
            'payer_name' => $data['cust_name'],
            'payer_email' => $data['cust_email'],
            'note' => 'PayStation অনলাইন পেমেন্ট' . ($this->payStation->isSandbox() ? ' (Sandbox)' : ''),
            'gateway' => self::GATEWAY,
            'invoice_number' => $invoiceNumber,
            'status' => 'initiated',
            'submitted_at' => Carbon::now('Asia/Dhaka'),
        ]);

        try {
            $result = $this->payStation->initiatePayment([
                'invoice_number' => $invoiceNumber,
                'amount' => $amount,
                'cust_name' => $data['cust_name'],
                'cust_phone' => $data['cust_phone'],
                'cust_email' => $data['cust_email'],
                'callback_url' => route('subscription.paystation.callback'),
                'reference' => 'Subscription #' . $subscription->id,
                'checkout_items' => 'Subscription renewal',
            ]);
        } catch (\Throwable $e) {
            report($e);

            $paymentRequest->update([
                'status' => 'failed',
                'reject_reason' => Str::limit($e->getMessage(), 490),
            ]);

            return back()
                ->withInput()
                ->withErrors(['paystation' => 'PayStation পেমেন্ট শুরু করা যায়নি: ' . $e->getMessage()]);
        }

        $paymentRequest->update(['gateway_response' => $result['response']]);

        return redirect()->away($result['payment_url']);
    }

    public function callback(Request $request): RedirectResponse
    {
        Log::info('PayStation callback', $request->query());

        $paymentRequest = SubscriptionPaymentRequest::with('subscription')
            ->where('gateway', self::GATEWAY)
            ->where('invoice_number', (string) $request->query('invoice_number'))
            ->firstOrFail();

        $result = $this->finalizer->finalize($paymentRequest, (string) $request->query('status'));

        return $this->redirectAfterCallback($paymentRequest, $result['ok'], $result['message']);
    }

    public function ipn(Request $request): JsonResponse
    {
        Log::info('PayStation IPN', $request->all());

        $paymentRequest = SubscriptionPaymentRequest::with('subscription')
            ->where('gateway', self::GATEWAY)
            ->where('invoice_number', (string) $request->input('invoice_number'))
            ->first();

        if (!$paymentRequest) {
            return response()->json(['status' => 'error', 'message' => 'Invoice not found'], 404);
        }

        $result = $this->finalizer->finalize($paymentRequest);

        if ($result['ok']) {
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error', 'message' => $result['message']], $result['retry'] ? 500 : 422);
    }

    public function verify(SubscriptionPaymentRequest $requestRow): RedirectResponse
    {
        abort_unless($requestRow->gateway === self::GATEWAY && $this->payStation->isEnabled(), 404);

        $requestRow->loadMissing('subscription');
        $result = $this->finalizer->finalize($requestRow);

        return $this->redirectAfterCallback($requestRow, $result['ok'], $result['message']);
    }

    protected function redirectAfterCallback(SubscriptionPaymentRequest $paymentRequest, bool $ok, string $message): RedirectResponse
    {
        $paymentRequest->refresh();
        $invoiceUrl = $ok && filled($paymentRequest->invoice_no) ? $this->invoices->url($paymentRequest) : null;

        if (auth('admin')->check()) {
            if (!$ok) {
                return RedirectHelper::routeWarning('admin.subscriptions.index', e($message));
            }

            $html = e($message);
            if ($invoiceUrl) {
                $html .= ' <a href="' . e($invoiceUrl) . '" target="_blank" class="alert-link">Invoice দেখুন / প্রিন্ট করুন</a>';
            }

            return RedirectHelper::routeSuccess('admin.subscriptions.index', $html);
        }

        $redirect = redirect()->route('subscription.payment.public', $paymentRequest->subscription->public_token);

        return $ok
            ? $redirect->with('success', $message)->with('invoice_url', $invoiceUrl)
            : $redirect->withErrors(['paystation' => $message]);
    }
}
