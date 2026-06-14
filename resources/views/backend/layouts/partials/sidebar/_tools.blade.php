@php
    $hasTools = ($userGuard->can('number_categories.index') || $userGuard->can('number_categories.create'))
        || ($userGuard->can('phone_numbers.index') || $userGuard->can('phone_numbers.create'));
@endphp
@if ($hasTools)
    @include('backend.layouts.partials.sidebar._section-title', [
        'title' => 'Tools',
        'sectionClass' => 'section-tools',
        'sectionKey' => 'tools',
        'icon' => 'fa-toolbox',
    ])

    @if ($userGuard->can('number_categories.index') || $userGuard->can('number_categories.create') || $userGuard->can('number_categories.edit') || $userGuard->can('number_categories.delete'))
        <li class="nav-item" data-sidebar-section="tools">
            <a class="nav-link menu-arrow {{ Route::is('admin.number_categories.*') ? 'active' : 'collapsed' }}"
               href="#sidebarNumberCategory" data-bs-toggle="collapse" role="button"
               aria-expanded="{{ Route::is('admin.number_categories.*') ? 'true' : 'false' }}" aria-controls="sidebarNumberCategory">
                <span class="nav-icon"><i class="fas fa-list-ol"></i></span>
                <span class="nav-text">Number Categories</span>
            </a>
            <div class="{{ Route::is('admin.number_categories.*') ? 'collapse show' : 'collapse' }}" id="sidebarNumberCategory">
                <ul class="nav sub-navbar-nav">
                    @if ($userGuard->can('number_categories.create'))
                        <li class="sub-nav-item"><a class="sub-nav-link" href="{{ route('admin.number_categories.create') }}">{{ __('language.create') }}</a></li>
                    @endif
                    @if ($userGuard->can('number_categories.index'))
                        <li class="sub-nav-item"><a class="sub-nav-link" href="{{ route('admin.number_categories.index') }}">{{ __('language.list') }}</a></li>
                    @endif
                </ul>
            </div>
        </li>
    @endif

    @if ($userGuard->can('phone_numbers.index') || $userGuard->can('phone_numbers.create') || $userGuard->can('phone_numbers.edit') || $userGuard->can('phone_numbers.delete'))
        <li class="nav-item" data-sidebar-section="tools">
            <a class="nav-link menu-arrow {{ Route::is('admin.phone_numbers.*') ? 'active' : 'collapsed' }}"
               href="#sidebarPhoneNumber" data-bs-toggle="collapse" role="button"
               aria-expanded="{{ Route::is('admin.phone_numbers.*') ? 'true' : 'false' }}" aria-controls="sidebarPhoneNumber">
                <span class="nav-icon"><i class="fas fa-phone-alt"></i></span>
                <span class="nav-text">Phone Numbers</span>
            </a>
            <div class="{{ Route::is('admin.phone_numbers.*') ? 'collapse show' : 'collapse' }}" id="sidebarPhoneNumber">
                <ul class="nav sub-navbar-nav">
                    @if ($userGuard->can('phone_numbers.create'))
                        <li class="sub-nav-item"><a class="sub-nav-link" href="{{ route('admin.phone_numbers.create') }}">{{ __('language.create') }}</a></li>
                    @endif
                    @if ($userGuard->can('phone_numbers.index'))
                        <li class="sub-nav-item"><a class="sub-nav-link" href="{{ route('admin.phone_numbers.index') }}">{{ __('language.list') }}</a></li>
                    @endif
                </ul>
            </div>
        </li>
    @endif
@endif
