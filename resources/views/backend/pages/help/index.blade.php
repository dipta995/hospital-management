@extends('backend.layouts.master')
@section('title')
    {{ $meta['title'] ?? t('help.title') }}
@endsection

@push('styles')
    @include('backend.pages.help.partials.styles')
@endpush

@section('admin-content')
<div class="doc-page container-fluid py-3">
    <div class="doc-hero">
        <h1>{{ $meta['title'] }}</h1>
        <p>{{ $meta['subtitle'] }}</p>
    </div>

    <div class="doc-toolbar">
        <div class="doc-search">
            <i class="fas fa-search"></i>
            <input type="text" id="doc-module-search" placeholder="{{ __('documentation.labels.search_placeholder') }}" autocomplete="off">
        </div>
        <div class="doc-lang-toggle btn-group">
            <a href="{{ route('admin.help.index') }}?lang=en" class="btn btn-sm {{ $locale === 'en' ? 'btn-dark' : 'btn-outline-secondary' }}">English</a>
            <a href="{{ route('admin.help.index') }}?lang=bn" class="btn btn-sm {{ $locale === 'bn' ? 'btn-dark' : 'btn-outline-secondary' }}">বাংলা</a>
        </div>
    </div>

    @php
        $grouped = collect($modules)->groupBy('group');
    @endphp

    @foreach($navGroups as $groupKey => $groupLabel)
        @if(!empty($grouped[$groupKey]))
            <h2 class="h6 text-uppercase text-muted fw-bold mb-3 mt-4" style="letter-spacing:0.06em;">{{ $groupLabel }}</h2>
            <div class="doc-grid mb-2">
                @foreach($grouped[$groupKey] as $mod)
                    <a href="{{ route('admin.help.show', [$locale, $mod['key']]) }}"
                       class="doc-module-card"
                       data-search="{{ strtolower($mod['title'].' '.$mod['summary']) }}">
                        <div class="doc-module-icon" style="background:{{ $mod['bg'] ?? '#f1f5f9' }};color:{{ $mod['color'] ?? '#0f172a' }}">
                            <i class="fas {{ $mod['icon'] ?? 'fa-book' }}"></i>
                        </div>
                        <h3>{{ $mod['title'] }}</h3>
                        <p>{{ $mod['summary'] }}</p>
                        <span class="doc-arrow">{{ __('documentation.labels.read_guide') }} <i class="fas fa-arrow-right ms-1"></i></span>
                    </a>
                @endforeach
            </div>
        @endif
    @endforeach

    <div id="doc-no-results" class="text-center text-muted py-5 d-none">
        <i class="fas fa-search fa-2x mb-3 opacity-50"></i>
        <p class="mb-0">{{ $locale === 'bn' ? 'কোনো মডিউল পাওয়া যায়নি' : 'No modules found' }}</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var input = document.getElementById('doc-module-search');
    var cards = document.querySelectorAll('.doc-module-card');
    var noResults = document.getElementById('doc-no-results');

    input?.addEventListener('input', function () {
        var q = (input.value || '').toLowerCase().trim();
        var visible = 0;
        cards.forEach(function (card) {
            var match = !q || (card.getAttribute('data-search') || '').indexOf(q) !== -1;
            card.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        if (noResults) noResults.classList.toggle('d-none', visible > 0);
    });
});
</script>
@endpush
