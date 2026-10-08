<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionPaymentRequest;
use App\Services\PaymentGatewayMigrationService;
use App\Services\SubscriptionInvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SubscriptionInvoiceController extends Controller
{
    public function show(
        Request $request,
        string $token,
        SubscriptionPaymentRequest $requestRow,
        SubscriptionInvoiceService $invoices,
        PaymentGatewayMigrationService $gatewayDb
    ) {
        abort_unless($gatewayDb->isInstalled(), 404);

        $subscription = Subscription::where('public_token', $token)->firstOrFail();

        if ((int) $requestRow->subscription_id !== (int) $subscription->id || $requestRow->status !== 'approved') {
            abort(404);
        }

        $requestRow->setRelation('subscription', $subscription);
        $invoices->assign($requestRow);
        [$periodStart, $periodEnd] = $invoices->period($requestRow);

        $setting = fn (string $key) => $invoices->setting($subscription, $key);

        $logo = $setting('logo');
        $logoPath = $logo ? public_path('images/' . $logo) : null;

        $data = [
            'payment' => $requestRow,
            'subscription' => $subscription,
            'periodStart' => $periodStart,
            'periodEnd' => $periodEnd,
            'company' => $setting('company_name') ?: config('app.name'),
            'address' => $setting('address'),
            'phone' => trim(($setting('phone_one') ?? '') . ($setting('phone_two') ? ', ' . $setting('phone_two') : '')),
            'email' => $setting('email'),
            'logoPath' => $logoPath && is_file($logoPath) ? $logoPath : null,
            'logoUrl' => $logoPath && is_file($logoPath) ? asset('images/' . $logo) : null,
            'invoiceUrl' => $invoices->url($requestRow),
        ];

        if ($request->query('download') === 'pdf') {
            return Pdf::loadView('subscription.invoice', array_merge($data, ['isPdf' => true]))
                ->setPaper('a4')
                ->download($requestRow->invoice_no . '.pdf');
        }

        return view('subscription.invoice', array_merge($data, ['isPdf' => false]));
    }
}
