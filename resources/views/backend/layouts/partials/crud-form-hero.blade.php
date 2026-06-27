@php
    $formTitle = $formTitle ?? t('form.form');
    $formSubtitle = $formSubtitle ?? null;
    $formIcon = $formIcon ?? 'fa-pen-to-square';
    $formBackRoute = $formBackRoute ?? ($pageHeader['index_route'] ?? null);
    $formBackUrl = $formBackUrl ?? null;
    $formBackLabel = $formBackLabel ?? t('common.back_to_list');
@endphp

<div class="crud-hero">
    <div class="crud-hero-content">
        <div class="crud-hero-icon">
            <i class="fas {{ $formIcon }}"></i>
        </div>
        <div>
            <h1 class="crud-hero-title">{{ $formTitle }}</h1>
            @if(!empty($formSubtitle))
                <p class="crud-hero-subtitle">{{ $formSubtitle }}</p>
            @endif
        </div>
    </div>

    @if(!empty($formBackUrl) || !empty($formBackRoute))
        <div class="crud-hero-actions">
            <a href="{{ $formBackUrl ?? route($formBackRoute) }}" class="btn-crud-light">
                <i class="fas fa-arrow-left"></i> {{ $formBackLabel }}
            </a>
        </div>
    @endif
</div>
