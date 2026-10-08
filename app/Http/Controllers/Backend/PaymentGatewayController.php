<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\SmsService;

class PaymentGatewayController extends Controller
{
    public function testSms(SmsService $sms)
    {
        $this->ensureSuperAdmin();

        $result = $sms->sendToAdmins('Test SMS from ' . (config('app.name') ?: 'Diagnostic') . ' at ' . now('Asia/Dhaka')->format('d M Y h:i A'));

        return redirect()
            ->route('admin.home')
            ->with($result['ok'] ? 'success' : 'error', 'SMS: ' . $result['message']);
    }

    protected function ensureSuperAdmin(): void
    {
        if (!auth('admin')->check() || !auth('admin')->user()->hasRole('Super Admin')) {
            abort(403, 'Only Super Admin can send a test SMS.');
        }
    }
}
