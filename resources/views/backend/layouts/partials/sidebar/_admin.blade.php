@php
    $hasAdmin = ($userGuard->can('branches.index') || $userGuard->can('branches.create'))
        || ($userGuard->can('roles.index') || $userGuard->can('roles.create'))
        || ($userGuard->can('admins.index') || $userGuard->can('admins.create'));
@endphp
@if ($hasAdmin)
    @include('backend.layouts.partials.sidebar._section-title', [
        'title' => 'Administration',
        'sectionClass' => 'section-admin',
        'sectionKey' => 'admin',
        'icon' => 'fa-shield-alt',
    ])

    @if ($userGuard->can('branches.index') || $userGuard->can('branches.create') || $userGuard->can('branches.edit') || $userGuard->can('branches.delete'))
        <li class="nav-item" data-sidebar-section="admin">
            <a class="nav-link menu-arrow {{ Route::is('admin.branches.*') ? 'active' : 'collapsed' }}"
               href="#sidebarBranch" data-bs-toggle="collapse" role="button"
               aria-expanded="{{ Route::is('admin.branches.*') ? 'true' : 'false' }}" aria-controls="sidebarBranch">
                <span class="nav-icon"><i class="fas fa-code-branch"></i></span>
                <span class="nav-text">Branches</span>
            </a>
            <div class="{{ Route::is('admin.branches.*') ? 'collapse show' : 'collapse' }}" id="sidebarBranch">
                <ul class="nav sub-navbar-nav">
                    <li class="sub-nav-item"><a class="sub-nav-link" href="{{ route('admin.branches.create') }}">{{ __('language.create') }}</a></li>
                    <li class="sub-nav-item"><a class="sub-nav-link" href="{{ route('admin.branches.index') }}">{{ __('language.list') }}</a></li>
                </ul>
            </div>
        </li>
    @endif

    @if ($userGuard->can('roles.index') || $userGuard->can('roles.create') || $userGuard->can('roles.edit') || $userGuard->can('roles.delete'))
        <li class="nav-item" data-sidebar-section="admin">
            <a class="nav-link menu-arrow {{ Route::is('admin.roles.*') ? 'active' : 'collapsed' }}"
               href="#sidebarRole" data-bs-toggle="collapse" role="button"
               aria-expanded="{{ Route::is('admin.roles.*') ? 'true' : 'false' }}" aria-controls="sidebarRole">
                <span class="nav-icon"><i class="fas fa-user-shield"></i></span>
                <span class="nav-text">Roles</span>
            </a>
            <div class="{{ Route::is('admin.roles.*') ? 'collapse show' : 'collapse' }}" id="sidebarRole">
                <ul class="nav sub-navbar-nav">
                    <li class="sub-nav-item"><a class="sub-nav-link" href="{{ route('admin.roles.create') }}">{{ __('language.create') }}</a></li>
                    <li class="sub-nav-item"><a class="sub-nav-link" href="{{ route('admin.roles.index') }}">{{ __('language.list') }}</a></li>
                </ul>
            </div>
        </li>
    @endif

    @if ($userGuard->can('admins.index') || $userGuard->can('admins.create') || $userGuard->can('admins.edit') || $userGuard->can('admins.delete'))
        <li class="nav-item" data-sidebar-section="admin">
            <a class="nav-link menu-arrow {{ Route::is('admin.admins.*') ? 'active' : 'collapsed' }}"
               href="#sidebarAdmin" data-bs-toggle="collapse" role="button"
               aria-expanded="{{ Route::is('admin.admins.*') ? 'true' : 'false' }}" aria-controls="sidebarAdmin">
                <span class="nav-icon"><i class="fas fa-users-cog"></i></span>
                <span class="nav-text">Admin Users</span>
            </a>
            <div class="{{ Route::is('admin.admins.*') ? 'collapse show' : 'collapse' }}" id="sidebarAdmin">
                <ul class="nav sub-navbar-nav">
                    <li class="sub-nav-item"><a class="sub-nav-link" href="{{ route('admin.admins.create') }}">{{ __('language.create') }}</a></li>
                    <li class="sub-nav-item"><a class="sub-nav-link" href="{{ route('admin.admins.index') }}">{{ __('language.list') }}</a></li>
                </ul>
            </div>
        </li>
    @endif
@endif
