@extends('backend.layouts.master')

@section('title')
    সাবস্ক্রিপশন
@endsection

@php
    $isSuperAdmin = auth('admin')->check() && auth('admin')->user()->hasRole('Super Admin');
    $gatewayPaymentOn = app(\App\Services\PayStationService::class)->canPay($subscription);
    $currency = '৳ ';
    $requestCounts = $requestCounts ?? collect();

    $tone = 'success';
    $statusTitle = 'সাবস্ক্রিপশন সক্রিয় আছে';
    $statusIcon = 'fa-check-circle';
    $totalDays = 30;
    $usedPercent = 0;

    if ($subscription) {
        if ($subscriptionMeta['expired']) {
            $tone = 'danger';
            $statusTitle = 'সাবস্ক্রিপশনের মেয়াদ শেষ হয়ে গেছে';
            $statusIcon = 'fa-times-circle';
        } elseif (!empty($subscriptionMeta['is_last_day'])) {
            $tone = 'danger';
            $statusTitle = 'আজই মেয়াদের শেষ দিন';
            $statusIcon = 'fa-exclamation-triangle';
        } elseif (!empty($subscriptionMeta['show_popup'])) {
            $tone = 'warning';
            $statusTitle = 'শীঘ্রই মেয়াদ শেষ হবে';
            $statusIcon = 'fa-hourglass-half';
        }

        if ($subscription->start_date && $subscription->end_date) {
            $totalDays = max(1, (int) round($subscription->start_date->diffInDays($subscription->end_date)) + 1);
        }
        $usedPercent = $subscriptionMeta['expired']
            ? 100
            : min(100, max(0, round(($totalDays - $subscriptionMeta['days_left']) / $totalDays * 100)));
    }

    $statusMeta = [
        'approved' => ['label' => 'অনুমোদিত', 'class' => 'is-approved', 'icon' => 'fa-check'],
        'pending' => ['label' => 'অপেক্ষমাণ', 'class' => 'is-pending', 'icon' => 'fa-clock'],
        'initiated' => ['label' => 'অনলাইন পেমেন্ট চলমান', 'class' => 'is-pending', 'icon' => 'fa-spinner'],
        'rejected' => ['label' => 'বাতিল', 'class' => 'is-rejected', 'icon' => 'fa-times'],
        'failed' => ['label' => 'অনলাইন পেমেন্ট ব্যর্থ', 'class' => 'is-rejected', 'icon' => 'fa-times'],
    ];
@endphp

@push('styles')
<style>
    .subx { --subx-radius: 14px; --subx-border: #e5e7eb; --subx-card: #fff; --subx-muted: #6b7280; }
    html[data-bs-theme="dark"] .subx { --subx-border: rgba(255, 255, 255, 0.1); --subx-card: var(--bs-secondary-bg, #1e293b); --subx-muted: #9ca3af; }
    .subx-card, .subx-stat { box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04); }

    .subx-hero {
        position: relative;
        overflow: hidden;
        border-radius: var(--subx-radius);
        padding: 1.5rem 1.6rem;
        color: #fff;
        margin-bottom: 1rem;
        box-shadow: 0 10px 30px -12px rgba(15, 23, 42, 0.35);
    }
    .subx-hero--success { background: linear-gradient(135deg, #0f766e 0%, #10b981 100%); }
    .subx-hero--warning { background: linear-gradient(135deg, #b45309 0%, #f59e0b 100%); }
    .subx-hero--danger { background: linear-gradient(135deg, #991b1b 0%, #ef4444 100%); }
    .subx-hero::after {
        content: "";
        position: absolute;
        right: -60px;
        top: -60px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.09);
        pointer-events: none;
    }
    .subx-hero__row { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1.25rem; position: relative; z-index: 1; }
    .subx-hero__main { display: flex; align-items: center; gap: 1rem; min-width: 0; }
    .subx-hero__icon {
        flex: 0 0 56px;
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: grid;
        place-items: center;
        font-size: 1.6rem;
        background: rgba(255, 255, 255, 0.18);
    }
    .subx-hero__eyebrow { font-size: 0.8rem; opacity: 0.85; letter-spacing: 0.02em; }
    .subx-hero__title { margin: 0.1rem 0 0.25rem; font-size: 1.35rem; font-weight: 700; color: #fff; }
    .subx-hero__sub { font-size: 0.9rem; opacity: 0.9; }
    .subx-hero__side { display: flex; align-items: center; gap: 1.25rem; }
    .subx-hero__days { text-align: center; line-height: 1.1; }
    .subx-hero__days strong { display: block; font-size: 2.4rem; font-weight: 800; }
    .subx-hero__days span { font-size: 0.85rem; opacity: 0.9; }
    .subx-hero .btn-light { font-weight: 700; color: #111827; border-radius: 10px; padding: 0.55rem 1.1rem; }
    .subx-progress { position: relative; z-index: 1; margin-top: 1.1rem; }
    .subx-progress__bar { height: 8px; border-radius: 999px; background: rgba(255, 255, 255, 0.25); overflow: hidden; }
    .subx-progress__bar > div { height: 100%; border-radius: 999px; background: #fff; }
    .subx-progress__legend { display: flex; justify-content: space-between; font-size: 0.78rem; opacity: 0.9; margin-top: 0.4rem; }

    .subx-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 0.85rem; margin-bottom: 1rem; }
    .subx-stat {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        padding: 0.95rem 1rem;
        border: 1px solid var(--subx-border);
        border-radius: var(--subx-radius);
        background: var(--subx-card);
    }
    .subx-stat__icon { flex: 0 0 42px; width: 42px; height: 42px; border-radius: 12px; display: grid; place-items: center; font-size: 1.05rem; }
    .subx-stat__icon.is-blue { background: #e0ecff; color: #2563eb; }
    .subx-stat__icon.is-red { background: #fee2e2; color: #dc2626; }
    .subx-stat__icon.is-violet { background: #ede9fe; color: #7c3aed; }
    .subx-stat__icon.is-green { background: #dcfce7; color: #16a34a; }
    .subx-stat__label { font-size: 0.78rem; color: var(--subx-muted); }
    .subx-stat__value { font-size: 1.05rem; font-weight: 700; line-height: 1.3; }

    .subx-card { border: 1px solid var(--subx-border); border-radius: var(--subx-radius); background: var(--subx-card); margin-bottom: 1rem; overflow: hidden; }
    .subx-card__head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.5rem; padding: 1rem 1.25rem; border-bottom: 1px solid var(--subx-border); }
    .subx-card__title { display: flex; align-items: center; gap: 0.6rem; margin: 0; font-size: 1rem; font-weight: 700; }
    .subx-card__title i { width: 32px; height: 32px; border-radius: 10px; display: grid; place-items: center; font-size: 0.9rem; background: #eef2ff; color: #4f46e5; }
    .subx-card__body { padding: 1.15rem 1.25rem; }
    .subx-card .form-label { font-size: 0.82rem; font-weight: 600; margin-bottom: 0.3rem; }
    .subx-card .form-control { border-radius: 9px; }

    .subx-steps { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 0.75rem; margin-bottom: 1rem; }
    .subx-step { display: flex; align-items: flex-start; gap: 0.6rem; padding: 0.75rem; border: 1px dashed var(--subx-border); border-radius: 12px; }
    .subx-step__num { flex: 0 0 28px; width: 28px; height: 28px; border-radius: 50%; display: grid; place-items: center; font-weight: 800; font-size: 0.85rem; background: #eef2ff; color: #4f46e5; }
    .subx-step strong { display: block; font-size: 0.85rem; }
    .subx-step small { color: var(--subx-muted); font-size: 0.75rem; }
    @media (max-width: 767.98px) { .subx-steps { grid-template-columns: 1fr; } }

    .subx-empty-pay { text-align: center; padding: 1.5rem 1rem; color: var(--subx-muted); }
    .subx-empty-pay i { font-size: 2rem; margin-bottom: 0.5rem; display: block; }

    .subx-link-row + .subx-link-row { margin-top: 0.75rem; }
    .subx-link-row .input-group .form-control { font-size: 0.82rem; background: var(--bs-tertiary-bg, #f8fafc); }

    .subx-pills { display: flex; flex-wrap: wrap; gap: 0.4rem; }
    .subx-pill { display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.25rem 0.65rem; border-radius: 999px; font-size: 0.78rem; font-weight: 700; background: var(--bs-tertiary-bg, #f3f4f6); }
    .subx-pill b { font-weight: 800; }

    .subx-badge { display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.25rem 0.65rem; border-radius: 999px; font-size: 0.78rem; font-weight: 700; white-space: nowrap; }
    .subx-badge.is-approved { background: #dcfce7; color: #15803d; }
    .subx-badge.is-pending { background: #fef3c7; color: #b45309; }
    .subx-badge.is-rejected { background: #fee2e2; color: #b91c1c; }

    .subx-table { margin: 0; }
    .subx-table thead th {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: var(--subx-muted);
        background: var(--bs-tertiary-bg, #f8fafc);
        border-bottom: 1px solid var(--subx-border);
        padding: 0.75rem 1rem;
        white-space: nowrap;
    }
    .subx-table tbody td { padding: 0.85rem 1rem; vertical-align: middle; font-size: 0.9rem; border-color: var(--subx-border); }
    .subx-table tbody tr:hover td { background: var(--bs-tertiary-bg, #f8fafc); }
    .subx-table .subx-txn { font-weight: 700; word-break: break-all; }
    .subx-table .subx-sub { font-size: 0.78rem; color: var(--subx-muted); }
    .subx-table .subx-amount { font-weight: 700; font-variant-numeric: tabular-nums; white-space: nowrap; }
    .subx-actions { display: inline-flex; flex-wrap: wrap; gap: 0.35rem; }
    .subx-actions .btn { border-radius: 8px; font-weight: 600; }
    .subx-pagination { padding: 0.85rem 1rem; border-top: 1px solid var(--subx-border); }

    @media (max-width: 1199.98px) { .subx-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 575.98px) {
        .subx-stats { gap: 0.6rem; }
        .subx-stat { padding: 0.75rem; gap: 0.6rem; }
        .subx-stat__icon { flex-basis: 34px; width: 34px; height: 34px; font-size: 0.9rem; }
        .subx-stat__value { font-size: 0.92rem; }
        .subx-hero { padding: 1.2rem; }
        .subx-hero__side { width: 100%; justify-content: space-between; }
    }
</style>
@endpush

@section('admin-content')
<div class="container-fluid subx">
    <div class="content-wrapper">
        @include('backend.layouts.partials.message')

        @if($subscription)
            <div class="subx-hero subx-hero--{{ $tone }}">
                <div class="subx-hero__row">
                    <div class="subx-hero__main">
                        <div class="subx-hero__icon"><i class="fas {{ $statusIcon }}"></i></div>
                        <div class="min-w-0">
                            <div class="subx-hero__eyebrow">সাবস্ক্রিপশন ব্যবস্থাপনা</div>
                            <h3 class="subx-hero__title">{{ $statusTitle }}</h3>
                            <div class="subx-hero__sub">
                                মেয়াদ: {{ $subscription->start_date?->format('d M Y') }} &ndash; {{ $subscription->end_date?->format('d M Y') }}
                            </div>
                        </div>
                    </div>
                    <div class="subx-hero__side">
                        <div class="subx-hero__days">
                            <strong>{{ $subscriptionMeta['expired'] ? 0 : $subscriptionMeta['days_left'] }}</strong>
                            <span>দিন বাকি</span>
                        </div>
                        @if($gatewayPaymentOn)
                            <a href="#subx-pay" class="btn btn-light">
                                <i class="fas fa-bolt me-1"></i> এখনই পেমেন্ট করুন
                            </a>
                        @endif
                    </div>
                </div>
                <div class="subx-progress">
                    <div class="subx-progress__bar"><div style="width: {{ $usedPercent }}%"></div></div>
                    <div class="subx-progress__legend">
                        <span>ব্যবহৃত {{ $subscriptionMeta['days_used'] }} দিন</span>
                        <span>মোট {{ $totalDays }} দিন</span>
                    </div>
                </div>
            </div>

            <div class="subx-stats">
                <div class="subx-stat">
                    <div class="subx-stat__icon is-blue"><i class="fas fa-calendar-plus"></i></div>
                    <div>
                        <div class="subx-stat__label">শুরুর তারিখ</div>
                        <div class="subx-stat__value">{{ $subscription->start_date?->format('d M Y') }}</div>
                    </div>
                </div>
                <div class="subx-stat">
                    <div class="subx-stat__icon is-red"><i class="fas fa-calendar-times"></i></div>
                    <div>
                        <div class="subx-stat__label">শেষ তারিখ</div>
                        <div class="subx-stat__value">{{ $subscription->end_date?->format('d M Y') }}</div>
                    </div>
                </div>
                <div class="subx-stat">
                    <div class="subx-stat__icon is-violet"><i class="fas fa-chart-bar"></i></div>
                    <div>
                        <div class="subx-stat__label">ব্যবহৃত দিন</div>
                        <div class="subx-stat__value">{{ $subscriptionMeta['days_used'] }} / {{ $totalDays }}</div>
                    </div>
                </div>
                <div class="subx-stat">
                    <div class="subx-stat__icon is-green"><i class="fas fa-wallet"></i></div>
                    <div>
                        <div class="subx-stat__label">নির্ধারিত পেমেন্ট</div>
                        <div class="subx-stat__value">{{ $currency }}{{ number_format((float) ($subscription->payment_amount ?? 0), 2) }}</div>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-warning">কোনো সাবস্ক্রিপশন পাওয়া যায়নি। আগে শুরুর তারিখ সেট করুন।</div>
        @endif

        <div class="row g-3">
            @if($subscription)
                <div class="{{ $isSuperAdmin ? 'col-xl-7' : 'col-12' }}" id="subx-pay">
                    <div class="subx-card h-100">
                        <div class="subx-card__head">
                            <h5 class="subx-card__title"><i class="fas fa-credit-card"></i> পেমেন্ট করুন</h5>
                        </div>
                        <div class="subx-card__body">
                            @if($gatewayPaymentOn)
                                @include('subscription.partials.paystation-button', ['subscription' => $subscription])
                                <div class="subx-steps">
                                    <div class="subx-step">
                                        <span class="subx-step__num">১</span>
                                        <div><strong>অনলাইনে পেমেন্ট</strong><small>bKash, Nagad, Rocket বা কার্ড</small></div>
                                    </div>
                                    <div class="subx-step">
                                        <span class="subx-step__num">২</span>
                                        <div><strong>সাথে সাথে নবায়ন</strong><small>অনুমোদনের অপেক্ষা নেই</small></div>
                                    </div>
                                    <div class="subx-step">
                                        <span class="subx-step__num">৩</span>
                                        <div><strong>Invoice তৈরি</strong><small>প্রিন্ট ও PDF ডাউনলোড</small></div>
                                    </div>
                                </div>
                                @if(!empty($subscription->payment_rules))
                                    <div class="small text-muted">
                                        <strong>পেমেন্টের নিয়মাবলি:</strong><br>{!! nl2br(e($subscription->payment_rules)) !!}
                                    </div>
                                @endif
                            @else
                                <form method="POST" action="{{ route('subscription.payment.public.submit', $subscription->public_token) }}">
                                    @csrf
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label class="form-label">ট্রানজেকশন আইডি</label>
                                            <input type="text" name="transaction_id" class="form-control" value="{{ old('transaction_id') }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">ট্রানজেকশনের তারিখ</label>
                                            <input type="date" name="transaction_date" class="form-control" value="{{ old('transaction_date', now('Asia/Dhaka')->format('Y-m-d')) }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">পরিমাণ</label>
                                            <input type="number" step="0.01" min="0" name="amount" class="form-control" value="{{ old('amount', $subscription->payment_amount) }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">প্রেরকের নম্বর</label>
                                            <input type="text" name="sender_number" class="form-control" value="{{ old('sender_number') }}" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">নোট (ঐচ্ছিক)</label>
                                            <textarea name="note" class="form-control" rows="2">{{ old('note') }}</textarea>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-success mt-3">
                                        <i class="fas fa-paper-plane me-1"></i> পেমেন্ট জমা দিন
                                    </button>
                                </form>

                                @if(!empty($subscriptionMeta['payment_url']))
                                    <div class="subx-link-row mt-3">
                                        <label class="form-label">পেমেন্ট করার লিংক</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="paymentFormUrl" readonly value="{{ $subscriptionMeta['payment_url'] }}">
                                            <button type="button" class="btn btn-outline-secondary" id="copyPaymentFormUrl">কপি</button>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if($isSuperAdmin)
                <div class="{{ $subscription ? 'col-xl-5' : 'col-12' }}">
                    <div class="subx-card h-100">
                        <div class="subx-card__head">
                            <h5 class="subx-card__title"><i class="fas fa-sliders-h"></i> সাবস্ক্রিপশন সেটিং</h5>
                            <span class="subx-pill"><i class="fas fa-user-shield"></i> Super Admin</span>
                        </div>
                        <div class="subx-card__body">
                            <form method="POST" action="{{ route('admin.subscriptions.date.update') }}">
                                @csrf
                                <div class="row g-2">
                                    <div class="col-sm-6">
                                        <label class="form-label">শুরুর তারিখ</label>
                                        <input type="date" name="start_date" class="form-control" value="{{ old('start_date', optional($subscription?->start_date)->format('Y-m-d')) }}" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label">পেমেন্টের পরিমাণ</label>
                                        <input type="number" step="0.01" min="0" name="payment_amount" class="form-control" value="{{ old('payment_amount', $subscription?->payment_amount ?? '') }}" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">পেমেন্টের নিয়মাবলি <span class="text-muted fw-normal">(পপআপে দেখানো হবে)</span></label>
                                        <textarea class="form-control" name="payment_rules" rows="2" placeholder="উদাহরণ: শেষ তারিখের আগেই পেমেন্ট সম্পন্ন করতে হবে।">{{ old('payment_rules', $subscription?->payment_rules ?? '') }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">লেনদেনের তথ্য জমার নির্দেশনা</label>
                                        <textarea class="form-control" name="transaction_details_note" rows="2" placeholder="উদাহরণ: ট্রানজেকশন আইডি, পরিমাণ, প্রেরকের নম্বর ও তারিখ লিখে দিন।">{{ old('transaction_details_note', $subscription?->transaction_details_note ?? '') }}</textarea>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary mt-3">
                                    <i class="fas fa-save me-1"></i> সংরক্ষণ করুন
                                </button>
                            </form>

                            @if(!empty($subscriptionMeta['payment_url']))
                                <hr class="my-3">
                                <div class="subx-link-row">
                                    <label class="form-label">পাবলিক পেমেন্ট লিংক</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control" id="subscriptionPaymentUrl" readonly value="{{ $subscriptionMeta['payment_url'] }}">
                                        <button type="button" class="btn btn-outline-secondary" id="copySubscriptionUrl">কপি</button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="subx-card mt-3">
            <div class="subx-card__head">
                <h5 class="subx-card__title"><i class="fas fa-receipt"></i> পেমেন্টের ইতিহাস</h5>
                @if($requestCounts->isNotEmpty())
                    <div class="subx-pills">
                        <span class="subx-pill">মোট <b>{{ $requestCounts->sum() }}</b></span>
                        @foreach(['approved', 'pending', 'initiated', 'failed', 'rejected'] as $key)
                            @if(!empty($requestCounts[$key]))
                                <span class="subx-badge {{ $statusMeta[$key]['class'] }}">
                                    {{ $statusMeta[$key]['label'] }} {{ $requestCounts[$key] }}
                                </span>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            @if($requests instanceof \Illuminate\Pagination\LengthAwarePaginator && $requests->count())
                @php $gatewayDbInstalled = app(\App\Services\PaymentGatewayMigrationService::class)->isInstalled(); @endphp
                <div class="table-responsive">
                    <table class="table subx-table align-middle">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>ট্রানজেকশন</th>
                            <th>তারিখ</th>
                            <th>পরিমাণ</th>
                            <th>জমাদানকারী</th>
                            <th>অবস্থা</th>
                            <th class="text-end">অ্যাকশন</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($requests as $item)
                            @php $st = $statusMeta[$item->status] ?? $statusMeta['pending']; @endphp
                            <tr>
                                <td class="text-muted">{{ $requests->firstItem() + $loop->index }}</td>
                                <td>
                                    <div class="subx-txn">{{ $item->transaction_id }}</div>
                                    @if($item->payment_method || $item->gateway)
                                        <div class="subx-sub">
                                            @if($item->gateway === 'paystation')
                                                <i class="fas fa-globe me-1"></i>PayStation
                                            @endif
                                            @if($item->payment_method)
                                                &middot; {{ $item->payment_method }}
                                            @endif
                                        </div>
                                    @endif
                                    @if($item->note && $item->gateway !== 'paystation')
                                        <div class="subx-sub">{{ \Illuminate\Support\Str::limit($item->note, 80) }}</div>
                                    @endif
                                    @if(in_array($item->status, ['rejected', 'failed'], true) && $item->reject_reason)
                                        <div class="subx-sub text-danger">কারণ: {{ $item->reject_reason }}</div>
                                    @endif
                                </td>
                                <td class="text-nowrap">{{ optional($item->transaction_date)->format('d M Y') ?? '—' }}</td>
                                <td class="subx-amount">{{ $currency }}{{ number_format((float) $item->amount, 2) }}</td>
                                <td>
                                    <div>{{ $item->submittedByAdmin->name ?? ($item->payer_name ?: 'Public Link') }}</div>
                                    @if($item->sender_number)
                                        <div class="subx-sub"><i class="fas fa-phone me-1"></i>{{ $item->sender_number }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="subx-badge {{ $st['class'] }}"><i class="fas {{ $st['icon'] }}"></i> {{ $st['label'] }}</span>
                                </td>
                                <td class="text-end">
                                    @if($item->status === 'pending' && !$isSuperAdmin)
                                        <span class="text-muted small">অনুমোদনের অপেক্ষায়</span>
                                    @elseif($item->status === 'pending')
                                        <div class="subx-actions">
                                            <form action="{{ route('admin.subscriptions.requests.approve', $item->id) }}" method="POST" class="d-inline-block">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-check me-1"></i>অনুমোদন</button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $item->id }}">
                                                <i class="fas fa-times me-1"></i>বাতিল
                                            </button>
                                        </div>

                                        <div class="modal fade text-start" id="rejectModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form action="{{ route('admin.subscriptions.requests.reject', $item->id) }}" method="POST" class="modal-content">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">পেমেন্ট অনুরোধ বাতিল করুন</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <label class="form-label">কারণ</label>
                                                        <textarea class="form-control" name="reject_reason" rows="3" required></textarea>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বন্ধ করুন</button>
                                                        <button type="submit" class="btn btn-danger">বাতিল করুন</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @elseif($item->status === 'initiated' && $item->gateway === 'paystation')
                                        <form action="{{ route('admin.subscriptions.requests.paystation-verify', $item->id) }}" method="POST" class="d-inline-block">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-sync-alt me-1"></i>যাচাই করুন
                                            </button>
                                        </form>
                                    @elseif($item->status === 'approved' && $gatewayDbInstalled)
                                        <a href="{{ route('subscription.invoice', ['token' => $subscription->public_token, 'requestRow' => $item->id]) }}"
                                           target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-file-invoice me-1"></i>{{ $item->invoice_no ?: 'Invoice' }}
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @if($requests->hasPages())
                    <div class="subx-pagination d-flex justify-content-end">
                        {!! $requests->links() !!}
                    </div>
                @endif
            @else
                <div class="subx-empty-pay">
                    <i class="fas fa-inbox"></i>
                    এখনও কোনো পেমেন্ট জমা হয়নি।
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        function bindCopy(buttonId, inputId, defaultLabel) {
            const copyButton = document.getElementById(buttonId);
            if (!copyButton) return;

            copyButton.addEventListener('click', function () {
                const input = document.getElementById(inputId);
                input.select();
                document.execCommand('copy');
                copyButton.textContent = 'কপি হয়েছে';
                setTimeout(function () {
                    copyButton.textContent = defaultLabel;
                }, 1200);
            });
        }

        bindCopy('copySubscriptionUrl', 'subscriptionPaymentUrl', 'কপি');
        bindCopy('copyPaymentFormUrl', 'paymentFormUrl', 'কপি');    });
</script>
@endpush
