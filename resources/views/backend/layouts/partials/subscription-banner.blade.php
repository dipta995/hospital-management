@if(!empty($subscriptionMeta['has_subscription']) && !empty($subscriptionMeta['expired']))
    <div class="subscription-renewal-bar">
        <div class="subscription-renewal-bar__inner is-expired">
            <div class="subscription-renewal-bar__message">
                <span class="subscription-renewal-bar__icon" aria-hidden="true">
                    <i class="fas fa-exclamation-triangle"></i>
                </span>
                <span class="subscription-renewal-bar__text">
                    সাবস্ক্রিপশনের মেয়াদ শেষ হয়ে গেছে:
                    <strong>{{ $subscriptionMeta['end_date_pretty'] }}</strong>
                    <span class="subscription-renewal-bar__days">— সেবা চালু রাখতে এখনই পেমেন্ট করুন।</span>
                </span>
            </div>
            <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-sm subscription-renewal-bar__btn">
                পেমেন্ট করুন
            </a>
        </div>
    </div>
@elseif(!empty($subscriptionMeta['show_banner']))
    <div class="subscription-renewal-bar">
        <div class="subscription-renewal-bar__inner">
            <div class="subscription-renewal-bar__message">
                <span class="subscription-renewal-bar__icon" aria-hidden="true">
                    <i class="fas fa-clock"></i>
                </span>
                <span class="subscription-renewal-bar__text">
                    সাবস্ক্রিপশন মেয়াদ শেষ:
                    <strong>{{ $subscriptionMeta['end_date_pretty'] }}</strong>
                    @if(!empty($subscriptionMeta['days_left']))
                        <span class="subscription-renewal-bar__days">({{ $subscriptionMeta['days_left'] }} দিন বাকি)</span>
                    @endif
                </span>
            </div>
            @if(!empty($subscriptionMeta['payment_url']))
                <a href="{{ $subscriptionMeta['payment_url'] }}" class="btn btn-sm subscription-renewal-bar__btn">
                    পেমেন্ট করুন
                </a>
            @endif
        </div>
    </div>
@endif
