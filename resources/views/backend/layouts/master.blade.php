<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <!-- Title Meta -->
    <meta charset="utf-8" />
    <title>@yield('title', 'Diagnosis')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully responsive premium admin dashboard template" />
    <meta name="author" content="Techzaa" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    @include('backend.layouts.partials.style')
    @stack('styles')
</head>

<body>

@php
    $subscriptionMeta = $subscriptionMeta ?? null;
@endphp

@if($subscriptionMeta && !empty($subscriptionMeta['show_banner']))
    <div style="position: fixed; top: 0; left: 0; width: 100%; min-height: 60px; z-index: 1060; background: linear-gradient(90deg, #991b1b 0%, #dc2626 100%); color: #ffffff; display: flex; align-items: center; justify-content: center; padding: 10px 18px; box-shadow: 0 6px 18px rgba(220, 38, 38, 0.35); border-bottom: 3px solid #7f1d1d;">
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 18px; width: 100%; max-width: 1260px; flex-wrap: wrap; text-align: center;">
            <div style="display: flex; align-items: center; gap: 14px; flex-wrap: wrap; justify-content: center; font-weight: 700; font-size: 20px; line-height: 1.35;">
                <span>আপনার সাবস্ক্রিপশনের মেয়াদ</span>
                <span style="display: inline-flex; align-items: center; justify-content: center; background: #fee2e2; color: #991b1b; padding: 8px 18px; border-radius: 999px; font-size: 24px; font-weight: 800; box-shadow: 0 4px 12px rgba(127, 29, 29, 0.25); border: 2px solid #fecaca;">{{ $subscriptionMeta['end_date_pretty'] }}</span>
                <span style="font-size: 0.96rem; color: rgba(255,255,255,0.95);">তারিখে শেষ হবে। সেবা বন্ধ হওয়া এড়াতে দ্রুত পেমেন্ট সম্পন্ন করুন।</span>
            </div>
            @if(!empty($subscriptionMeta['payment_url']))
                <a href="{{ $subscriptionMeta['payment_url'] }}" style="display: inline-flex; align-items: center; justify-content: center; background: #ffffff; color: #dc2626; font-weight: 700; padding: 12px 20px; border-radius: 999px; text-decoration: none; box-shadow: 0 8px 18px rgba(255,255,255,0.24); transition: transform .15s ease;">
                    পেমেন্ট করুন
                </a>
            @endif
        </div>
    </div>
@endif

<!-- START Wrapper -->
<div class="wrapper" @if($subscriptionMeta && !empty($subscriptionMeta['show_banner'])) style="padding-top: 70px;" @endif>

    <!-- ========== Topbar Start ========== -->
    @include('backend.layouts.partials.navbar')
    <!-- ========== Topbar End ========== -->

    <!-- ========== App Menu Start ========== -->
    @include('backend.layouts.partials.sidebar')
    <!-- ========== App Menu End ========== -->

    <!-- ==================================================== -->
    <!-- Start right Content here -->
    <!-- ==================================================== -->
    <div class="page-content">

        <!-- Start Container Fluid -->
        @yield('admin-content')
        <!-- End Container Fluid -->

        <!-- ========== Footer Start ========== -->
       @include('backend.layouts.partials.footer')
        <!-- ========== Footer End ========== -->

    </div>
    <!-- ==================================================== -->
    <!-- End Page Content -->
    <!-- ==================================================== -->

</div>
@include('backend.layouts.partials.script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
@include('backend.layouts.partials.auth-feedback')
@if($subscriptionMeta && !empty($subscriptionMeta['show_popup']) && empty($subscriptionMeta['expired']))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const meta = @json($subscriptionMeta);
        const key = 'subscription_popup_last_seen_' + (meta.subscription_id || 'global');
        const now = Date.now();
        const lastSeen = parseInt(localStorage.getItem(key) || '0', 10);
        const oneHour = 60 * 60 * 1000;

        if (lastSeen && (now - lastSeen) < oneHour) {
            return;
        }

        const paymentRules = meta.payment_rules ? String(meta.payment_rules).replace(/\n/g, '<br>') : 'মেয়াদ শেষ হওয়ার আগে পেমেন্ট সম্পন্ন করুন এবং সঠিক ট্রানজেকশন তথ্য শেয়ার করুন।';
        const transactionNote = meta.transaction_details_note ? String(meta.transaction_details_note).replace(/\n/g, '<br>') : 'ট্রানজেকশন আইডি, পরিমাণ, প্রেরকের নম্বর এবং তারিখ লিখে দিন।';

        Swal.fire({
            icon: 'warning',
            title: 'সাবস্ক্রিপশন নবায়ন স্মরণবার্তা',
            html: '<div style="text-align:left">'
                + '<p><strong>শেষ তারিখ:</strong> ' + meta.end_date_pretty + '</p>'
                + '<p><strong>পেমেন্টের পরিমাণ:</strong> ' + (meta.payment_amount ?? 0) + '</p>'
                + '<p><strong>পেমেন্টের নিয়মাবলি:</strong><br>' + paymentRules + '</p>'
                + '<p><strong>লেনদেনের তথ্য:</strong><br>' + transactionNote + '</p>'
                + '</div>',
            confirmButtonText: 'ঠিক আছে'
        }).then(function () {
        });

        localStorage.setItem(key, String(now));
    });
</script>
@endif
@stack('scripts')
@include('backend.layouts.partials.ai-chat-widget')

</body>

</html>
