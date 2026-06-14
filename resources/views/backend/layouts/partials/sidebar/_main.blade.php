{{-- Main / account shortcuts --}}
@include('backend.layouts.partials.sidebar._section-title', [
    'title' => 'Main',
    'sectionClass' => 'section-main',
    'sectionKey' => 'main',
    'icon' => 'fa-home',
])

<li class="nav-item" data-sidebar-section="main">
    <a class="nav-link {{ Route::is('admin.home') ? 'active' : '' }}" href="{{ route('admin.home') }}">
        <span class="nav-icon"><iconify-icon icon="solar:home-2-broken"></iconify-icon></span>
        <span class="nav-text">Dashboard</span>
    </a>
</li>
<li class="nav-item {{ Route::is('admin.settings.edit', auth()->id()) ? 'active' : '' }}" data-sidebar-section="main">
    <a class="nav-link" href="{{ route('admin.settings.edit', auth()->id()) }}">
        <span class="nav-icon"><i class="fas fa-cog"></i></span>
        <span class="nav-text">Settings</span>
    </a>
</li>
<li class="nav-item {{ Route::is('admin.subscriptions.*') ? 'active' : '' }}" data-sidebar-section="main">
    <a class="nav-link" href="{{ route('admin.subscriptions.index') }}">
        <span class="nav-icon"><i class="fas fa-calendar-check"></i></span>
        <span class="nav-text">Subscription</span>
    </a>
</li>
<li class="nav-item {{ Route::is('admin.change') ? 'active' : '' }}" data-sidebar-section="main">
    <a class="nav-link" href="{{ route('admin.change') }}">
        <span class="nav-icon"><i class="fas fa-key"></i></span>
        <span class="nav-text">Update Password</span>
    </a>
</li>
