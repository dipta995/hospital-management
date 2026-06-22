@extends('backend.layouts.master')
@section('title')
    {{ $module['title'] }} — {{ $meta['title'] }}
@endsection

@push('styles')
    @include('backend.pages.help.partials.styles')
@endpush

@section('admin-content')
<div class="doc-page container-fluid py-3">
    <div class="doc-toolbar">
        <a href="{{ route('admin.help.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> {{ __('documentation.labels.all_modules') }}
        </a>
        <div class="doc-lang-toggle btn-group btn-group-sm">
            <a href="{{ route('admin.help.show', ['en', $module['key']]) }}" class="btn {{ $locale === 'en' ? 'btn-dark' : 'btn-outline-secondary' }}">English</a>
            <a href="{{ route('admin.help.show', ['bn', $module['key']]) }}" class="btn {{ $locale === 'bn' ? 'btn-dark' : 'btn-outline-secondary' }}">বাংলা</a>
        </div>
    </div>

    <div class="doc-layout">
        <aside class="doc-sidebar d-none d-lg-block">
            <div class="doc-sidebar-title">{{ __('documentation.labels.on_this_page') }}</div>

            @foreach($navGroups as $groupKey => $groupLabel)
                @php $groupMods = collect($modules)->where('group', $groupKey); @endphp
                @if($groupMods->isNotEmpty())
                    <div class="doc-nav-group">
                        <div class="doc-nav-group-label">{{ $groupLabel }}</div>
                        @foreach($groupMods as $mod)
                            <a href="{{ route('admin.help.show', [$locale, $mod['key']]) }}"
                               class="doc-nav-link {{ $mod['key'] === $module['key'] ? 'active' : '' }}">
                                {{ $mod['title'] }}
                            </a>
                        @endforeach
                    </div>
                @endif
            @endforeach
        </aside>

        <div class="doc-main">
            <div class="doc-mobile-nav">
                <select class="form-select form-select-sm" onchange="if(this.value) window.location=this.value">
                    @foreach($modules as $mod)
                        <option value="{{ route('admin.help.show', [$locale, $mod['key']]) }}" {{ $mod['key'] === $module['key'] ? 'selected' : '' }}>
                            {{ $mod['title'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <article class="doc-article">
                <header class="doc-article-header">
                    <div class="doc-breadcrumb">
                        <a href="{{ route('admin.help.index') }}">{{ $meta['title'] }}</a>
                        <span class="mx-1">/</span>
                        <span>{{ $module['title'] }}</span>
                    </div>
                    <div class="d-flex align-items-start gap-3">
                        <div class="doc-module-icon flex-shrink-0" style="background:{{ $module['bg'] ?? '#f1f5f9' }};color:{{ $module['color'] ?? '#0f172a' }};width:52px;height:52px;font-size:1.2rem;">
                            <i class="fas {{ $module['icon'] ?? 'fa-book' }}"></i>
                        </div>
                        <div>
                            <h1>{{ $module['title'] }}</h1>
                            @if(!empty($module['intro']))
                                <p class="lead">{{ $module['intro'] }}</p>
                            @elseif(!empty($module['summary']))
                                <p class="lead">{{ $module['summary'] }}</p>
                            @endif
                        </div>
                    </div>
                </header>

                <div class="doc-article-body">
                    @if(!empty($module['sections']))
                        <nav class="mb-4 pb-3 border-bottom">
                            <div class="small text-muted text-uppercase fw-bold mb-2" style="letter-spacing:0.05em;">{{ __('documentation.labels.on_this_page') }}</div>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($module['sections'] as $section)
                                    <a href="#{{ $section['id'] }}" class="badge bg-light text-dark border text-decoration-none fw-normal px-2 py-2">
                                        {{ $section['title'] }}
                                    </a>
                                @endforeach
                                @if(!empty($module['faqs']))
                                    <a href="#faq" class="badge bg-light text-dark border text-decoration-none fw-normal px-2 py-2">{{ __('documentation.labels.faq') }}</a>
                                @endif
                            </div>
                        </nav>
                    @endif

                    @include('backend.pages.help.partials.module-body', ['module' => $module])
                </div>
            </article>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var links = document.querySelectorAll('.doc-article-body a[href^="#"]');
    links.forEach(function (link) {
        link.addEventListener('click', function (e) {
            var id = link.getAttribute('href').slice(1);
            var el = document.getElementById(id);
            if (el) {
                e.preventDefault();
                el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
});
</script>
@endpush
