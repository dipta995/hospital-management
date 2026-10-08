@php
    $reminderSubscription = \App\Services\SubscriptionService::getCurrentForBranch(auth('admin')->user()?->branch_id);
    $reminderIsLastDay = !empty($subscriptionMeta['is_last_day']);
    $reminderCloseDelay = (int) ($subscriptionMeta['popup_close_delay'] ?? 5);
    $reminderGatewayOn = app(\App\Services\PayStationService::class)->canPay($reminderSubscription);
@endphp

@if($reminderSubscription)
<div class="modal fade subscription-reminder-modal" id="subscriptionReminderModal" tabindex="-1" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header {{ $reminderIsLastDay ? 'bg-danger' : 'bg-warning' }}">
                <h5 class="modal-title {{ $reminderIsLastDay ? 'text-white' : 'text-dark' }}">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    @if($reminderIsLastDay)
                        আজই সাবস্ক্রিপশনের শেষ দিন!
                    @else
                        সাবস্ক্রিপশন নবায়নের সময় হয়েছে
                    @endif
                </h5>
                <button type="button" class="btn-close {{ $reminderIsLastDay ? 'btn-close-white' : '' }} d-none"
                        data-reminder-close aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row g-2 mb-3">
                    <div class="col-sm-4">
                        <div class="subscription-reminder-stat">
                            <span>শেষ তারিখ</span>
                            <strong>{{ $subscriptionMeta['end_date_pretty'] }}</strong>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="subscription-reminder-stat {{ $reminderIsLastDay ? 'is-danger' : '' }}">
                            <span>বাকি</span>
                            <strong>{{ $reminderIsLastDay ? 'আজই শেষ' : $subscriptionMeta['days_left'] . ' দিন' }}</strong>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="subscription-reminder-stat">
                            <span>পেমেন্টের পরিমাণ</span>
                            <strong>৳ {{ number_format((float) ($reminderSubscription->payment_amount ?? 0), 2) }}</strong>
                        </div>
                    </div>
                </div>

                <p class="small mb-3">
                    মেয়াদ শেষ হলে সফটওয়্যার ব্যবহার বন্ধ হয়ে যাবে।
                    @if($reminderGatewayOn)
                        নিচের বাটন থেকে অনলাইনে পেমেন্ট করলে সাথে সাথে সাবস্ক্রিপশন নবায়ন হবে।
                    @else
                        সাবস্ক্রিপশন পেজ থেকে পেমেন্টের তথ্য জমা দিন।
                    @endif
                </p>

                @include('subscription.partials.paystation-button', ['subscription' => $reminderSubscription])

                @if(!empty($reminderSubscription->payment_rules))
                    <div class="small mb-2"><strong>পেমেন্টের নিয়মাবলি:</strong><br>{!! nl2br(e($reminderSubscription->payment_rules)) !!}</div>
                @endif
            </div>

            <div class="modal-footer justify-content-between">
                <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-outline-primary btn-sm">
                    {{ $reminderGatewayOn ? 'সাবস্ক্রিপশনের বিস্তারিত' : 'পেমেন্ট জমা দিন / বিস্তারিত' }}
                </a>
                <div class="d-flex align-items-center gap-2">
                    <span class="small text-muted" data-reminder-countdown>
                        <span data-reminder-seconds>{{ $reminderCloseDelay }}</span> সেকেন্ড পর বন্ধ করা যাবে
                    </span>
                    <button type="button" class="btn btn-secondary btn-sm d-none" data-reminder-close>বন্ধ করুন</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const el = document.getElementById('subscriptionReminderModal');
        if (!el) return;

        const meta = @json($subscriptionMeta);
        const storageKey = 'subscription_reminder_' + meta.subscription_id + '_' + meta.end_date;
        const intervalMs = () => (Date.now() >= meta.last_day_starts_at
            ? meta.popup_last_day_interval_minutes
            : meta.popup_normal_interval_minutes) * 60 * 1000;
        const closeDelay = parseInt(meta.popup_close_delay, 10) || 0;
        const bnDigits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        const toBn = (n) => String(n).replace(/\d/g, (d) => bnDigits[d]);

        Object.keys(localStorage)
            .filter((k) => (k.startsWith('subscription_reminder_') && k !== storageKey) || k.startsWith('subscription_popup_last_seen_'))
            .forEach((k) => localStorage.removeItem(k));

        const closeButtons = el.querySelectorAll('[data-reminder-close]');
        const countdownWrap = el.querySelector('[data-reminder-countdown]');
        const secondsEl = el.querySelector('[data-reminder-seconds]');
        const modal = window.bootstrap && bootstrap.Modal
            ? bootstrap.Modal.getOrCreateInstance(el, { backdrop: 'static', keyboard: false })
            : null;
        let isOpen = false;

        function lastSeen() {
            return parseInt(localStorage.getItem(storageKey) || '0', 10);
        }

        function hide() {
            if (modal) {
                modal.hide();
            } else {
                el.classList.remove('show');
                el.style.display = 'none';
                el.dispatchEvent(new Event('hidden.bs.modal'));
            }
        }

        function show() {
            if (isOpen) return;
            isOpen = true;
            closeButtons.forEach((b) => b.classList.add('d-none'));
            countdownWrap.classList.remove('d-none');

            let remaining = closeDelay;
            secondsEl.textContent = toBn(remaining);
            const timer = setInterval(function () {
                remaining -= 1;
                secondsEl.textContent = toBn(Math.max(remaining, 0));
                if (remaining <= 0) {
                    clearInterval(timer);
                    countdownWrap.classList.add('d-none');
                    closeButtons.forEach((b) => b.classList.remove('d-none'));
                }
            }, 1000);
            if (remaining <= 0) {
                clearInterval(timer);
                countdownWrap.classList.add('d-none');
                closeButtons.forEach((b) => b.classList.remove('d-none'));
            }

            if (modal) {
                modal.show();
            } else {
                el.style.display = 'block';
                el.classList.add('show');
            }
        }

        function schedule() {
            const elapsed = Date.now() - lastSeen();
            const interval = intervalMs();
            if (!lastSeen() || elapsed >= interval) {
                show();
                return;
            }
            let wait = interval - elapsed + 1000;
            if (Date.now() < meta.last_day_starts_at) {
                wait = Math.min(wait, meta.last_day_starts_at - Date.now() + 1000);
            }
            setTimeout(schedule, wait);
        }

        closeButtons.forEach((b) => b.addEventListener('click', hide));

        el.addEventListener('hidden.bs.modal', function () {
            isOpen = false;
            localStorage.setItem(storageKey, String(Date.now()));
            schedule();
        });

        schedule();
    });
</script>
@endif
