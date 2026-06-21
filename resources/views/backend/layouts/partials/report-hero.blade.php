@php
    $reportTitle = $reportTitle ?? tp($pageHeader['title'] ?? 'Report');
    $reportSubtitle = $reportSubtitle ?? t('common.filter_by_date');
    $reportIcon = $reportIcon ?? 'fa-chart-line';
    $resetRoute = $resetRoute ?? null;
@endphp
<div class="inv-hero">
    <div class="inv-hero-inner">
        <div class="inv-hero-left">
            <div class="inv-hero-icon"><i class="fas {{ $reportIcon }}"></i></div>
            <div>
                <h1 class="inv-hero-title">{{ $reportTitle }}</h1>
                <p class="inv-hero-sub">{{ $reportSubtitle }}</p>
            </div>
        </div>
        @if(!empty($resetRoute))
            <div class="inv-hero-actions">
                <a href="{{ $resetRoute }}" class="inv-btn-glass"><i class="fas fa-sync-alt"></i> {{ t('common.reset_filters') }}</a>
            </div>
        @endif
    </div>
</div>
