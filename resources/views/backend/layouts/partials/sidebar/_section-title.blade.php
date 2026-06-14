@php
    $sectionClass = $sectionClass ?? '';
    $sectionKey = $sectionKey ?? '';
    $icon = $icon ?? null;
@endphp
<li class="menu-title {{ $sectionClass }} sidebar-section-title" @if($sectionKey) data-sidebar-section="{{ $sectionKey }}" @endif>
    @if($icon)<i class="fas {{ $icon }}"></i>@endif
    {{ $title }}
</li>
