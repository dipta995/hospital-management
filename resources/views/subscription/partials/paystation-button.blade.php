@php
    $payStation = app(\App\Services\PayStationService::class);
    $payStationAdmin = auth('admin')->user();
    $payStationAmount = (float) ($subscription->payment_amount ?? 0);
    $payStationSetting = fn (string $key) => app(\App\Services\SubscriptionInvoiceService::class)->setting($subscription, $key);
@endphp

@if($payStation->isEnabled() && $payStationAmount > 0)
    <div class="p-3 border rounded mb-3" style="background:#f0fdf4; border-color:#86efac !important;">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
            <h6 class="mb-0">PayStation দিয়ে অনলাইনে পেমেন্ট করুন</h6>
            @if($payStation->isSandbox())
                <span class="badge bg-warning text-dark">Sandbox (Test Mode)</span>
            @endif
        </div>
        <p class="small text-muted mb-2">
            bKash, Nagad, Rocket, Upay বা কার্ড দিয়ে পেমেন্ট করুন। পেমেন্ট সফল হলে সাবস্ক্রিপশন স্বয়ংক্রিয়ভাবে নবায়ন হবে।
        </p>

        @error('paystation')
            <div class="alert alert-danger py-2">{{ $message }}</div>
        @enderror

        <form method="POST" action="{{ route('subscription.paystation.initiate', $subscription->public_token) }}">
            @csrf
            <div class="row g-2">
                <div class="col-md-4">
                    <label class="form-label small mb-1">নাম</label>
                    <input type="text" name="cust_name" class="form-control form-control-sm @error('cust_name') is-invalid @enderror"
                           value="{{ old('cust_name', $payStationAdmin->name ?? $payStationSetting('company_name')) }}" required>
                    @error('cust_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label small mb-1">মোবাইল নম্বর</label>
                    <input type="text" name="cust_phone" class="form-control form-control-sm @error('cust_phone') is-invalid @enderror"
                           value="{{ old('cust_phone', $payStationSetting('phone_one')) }}" required>
                    @error('cust_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label small mb-1">ইমেইল</label>
                    <input type="email" name="cust_email" class="form-control form-control-sm @error('cust_email') is-invalid @enderror"
                           value="{{ old('cust_email', $payStationAdmin->email ?? $payStationSetting('email')) }}" required>
                    @error('cust_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <button type="submit" class="btn btn-success mt-2">
                PayStation দিয়ে {{ number_format($payStationAmount, 2) }} টাকা পেমেন্ট করুন
            </button>
        </form>
    </div>
@endif
