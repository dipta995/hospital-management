@extends('backend.layouts.master')

@section('title')
    Security Settings
@endsection

@php
    $yesNo = function (string $name, string $label, string $help) use ($values) {
        return compact('name', 'label', 'help') + ['value' => $values[$name] ?? 'No'];
    };
    $alertToggles = [
        $yesNo('security_alert_delete', 'Invoice delete হলে SMS', 'কেউ invoice মুছলে সাথে সাথে Owner-এর ফোনে SMS যাবে।'),
        $yesNo('security_alert_edit_reduce', 'Bill কমালে SMS', 'Edit করে bill-এর টাকা কমালে Owner-এর ফোনে SMS যাবে।'),
        $yesNo('security_alert_cancel_request', 'Cancel request এলে SMS', 'Staff কোনো invoice বাতিলের request পাঠালে Owner জানতে পারবেন।'),
        $yesNo('security_alert_cash_short', 'Cash কম পড়লে SMS', 'Cash closing-এ গুনে কম টাকা পাওয়া গেলে Owner-এর ফোনে SMS যাবে।'),
        $yesNo('security_daily_summary', 'রাতে দিনের সারাংশ SMS', 'প্রতিদিনের bill, collection, discount, edit/delete-এর হিসাব Owner-এর ফোনে যাবে।'),
    ];
    $patientToggles = [
        $yesNo('security_patient_bill_sms', 'Invoice হলে রোগীকে bill-এর SMS', 'রোগী মোট bill, জমা আর বাকি টাকার SMS পাবে। পরে কেউ bill কমালে রোগীর SMS-এর সাথে মিলবে না, তাই চুরি ধরা পড়ে। চালু থাকলে পুরনো "Customer Invoice SMS" আর আলাদা করে যাবে না।'),
        $yesNo('security_payment_sms', 'বকেয়া জমা দিলে রোগীকে SMS', 'Due payment নিলে রোগী SMS পাবে, তাই টাকা নিয়ে entry না দেওয়া যাবে না।'),
    ];
    $controlToggles = [
        $yesNo('security_require_reason', 'Edit/Delete-এ কারণ বাধ্যতামূলক', 'Invoice delete আর cost edit বা delete করতে কারণ লিখতেই হবে। Invoice edit-এ কারণ ঐচ্ছিক। কারণটা Trash-এ দেখা যাবে।'),
        $yesNo('security_cancel_approval', 'Invoice delete-এ Owner-এর approval', 'Owner/Super Admin ছাড়া কেউ সরাসরি invoice মুছতে পারবে না, শুধু cancel request পাঠাতে পারবে।'),
    ];
@endphp

@section('admin-content')
    <div class="container-fluid py-3">
        @include('backend.layouts.partials.message')

        @if(!$tableReady)
            <div class="alert alert-warning">
                Security tables এখনো install করা হয়নি। Cancel request, cash closing আর login history কাজ করবে না। Support-এ যোগাযোগ করুন।
            </div>
        @endif

        <form method="POST" action="{{ route('admin.security.settings.update') }}">
            @csrf
            <div class="row g-3">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="mb-1"><i class="fas fa-bell text-danger"></i> Owner Alert (SMS)</h5>
                            <p class="text-muted small">SMS যায় এই branch-এর SMS balance থেকে (এখন বাকি <strong>{{ number_format($smsBalance) }}</strong>)।</p>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="security_owner_phones">Owner-এর মোবাইল নম্বর</label>
                                <input type="text" name="security_owner_phones" id="security_owner_phones" class="form-control"
                                       value="{{ old('security_owner_phones', $values['security_owner_phones']) }}" placeholder="01XXXXXXXXX, 01XXXXXXXXX">
                                <small class="text-muted">একাধিক নম্বর কমা দিয়ে লিখুন। নম্বর না দিলে কোনো alert যাবে না।</small>
                                @if(empty($ownerPhones))
                                    <div class="text-danger small mt-1"><i class="fas fa-triangle-exclamation"></i> এখনো কোনো নম্বর সেট করা নেই।</div>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="security_alert_discount_percent">বেশি discount হলে SMS (%)</label>
                                <input type="number" min="0" max="100" step="1" name="security_alert_discount_percent" id="security_alert_discount_percent"
                                       class="form-control" value="{{ old('security_alert_discount_percent', $values['security_alert_discount_percent']) }}">
                                <small class="text-muted">এই % বা তার বেশি discount দিলে Owner-এর ফোনে SMS যাবে। 0 দিলে বন্ধ।</small>
                            </div>

                            @include('backend.pages.security.partials.toggles', ['toggles' => $alertToggles])
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <h5 class="mb-1"><i class="fas fa-lock text-primary"></i> Lock ও Approval</h5>
                            <p class="text-muted small">Owner আর Super Admin-এর জন্য এগুলো প্রযোজ্য নয়।</p>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="security_invoice_lock_hours">কত ঘণ্টা পরে invoice/cost lock হবে</label>
                                <input type="number" min="0" max="720" name="security_invoice_lock_hours" id="security_invoice_lock_hours" class="form-control"
                                       value="{{ old('security_invoice_lock_hours', $values['security_invoice_lock_hours']) }}">
                                <small class="text-muted">এর পরে staff আর edit/delete করতে পারবে না। Cash closing হলে সেই দিনের invoice সাথে সাথে lock হয়। 0 দিলে সময়ের lock বন্ধ।</small>
                            </div>

                            @include('backend.pages.security.partials.toggles', ['toggles' => $controlToggles])
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="mb-1"><i class="fas fa-user-injured text-success"></i> রোগীকে SMS</h5>
                            <p class="text-muted small">রোগীর ফোন নম্বর ঠিক থাকলে তবেই SMS যায়।</p>
                            @include('backend.pages.security.partials.toggles', ['toggles' => $patientToggles])
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-3">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Security Settings</button>
            </div>
        </form>

        <form method="POST" action="{{ route('admin.security.test-alert') }}" class="mt-2 text-end">
            @csrf
            <button type="submit" class="btn btn-outline-danger btn-sm" @if(empty($ownerPhones)) disabled @endif>
                <i class="fas fa-paper-plane"></i> Owner-এর নম্বরে Test SMS পাঠান
            </button>
        </form>
    </div>
@endsection
