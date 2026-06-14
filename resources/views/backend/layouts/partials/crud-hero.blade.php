@php
    $heroTitle = $heroTitle ?? ($pageHeader['title'] ?? 'List');
    $heroSubtitle = $heroSubtitle ?? null;
    $heroIcon = $heroIcon ?? 'fa-list';
    $heroCreateRoute = $heroCreateRoute ?? ($pageHeader['create_route'] ?? null);
    $heroCreateLabel = $heroCreateLabel ?? 'Add New';
@endphp

<div class="crud-hero">
    <div class="crud-hero-content">
        <div class="crud-hero-icon">
            <i class="fas {{ $heroIcon }}"></i>
        </div>
        <div>
            <h1 class="crud-hero-title">{{ $heroTitle }}</h1>
            @if(!empty($heroSubtitle))
                <p class="crud-hero-subtitle">{{ $heroSubtitle }}</p>
            @endif
        </div>
    </div>

    @if(!empty($heroActions))
        <div class="crud-hero-actions">
            {!! $heroActions !!}
        </div>
    @elseif(!empty($heroCreateRoute))
        <div class="crud-hero-actions">
            <a href="{{ route($heroCreateRoute) }}" class="btn-crud-primary">
                <i class="fas fa-plus"></i> {{ $heroCreateLabel }}
            </a>
        </div>
    @endif
</div>
