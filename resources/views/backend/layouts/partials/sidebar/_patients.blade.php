{{-- Patients --}}
@if ($userGuard->can('admins.index') || $userGuard->can('admins.create') || $userGuard->can('admins.edit') || $userGuard->can('admins.delete'))
    @include('backend.layouts.partials.sidebar._section-title', [
        'title' => t('menu.patients'),
        'sectionClass' => 'section-patients',
        'sectionKey' => 'patients',
        'icon' => 'fa-user-injured',
    ])
    <li class="nav-item" data-sidebar-section="patients">
        <a class="nav-link menu-arrow {{ Route::is('admin.users.*') ? 'active' : 'collapsed' }}"
           href="#sidebarUser" data-bs-toggle="collapse" role="button"
           aria-expanded="{{ Route::is('admin.users.*') ? 'true' : 'false' }}" aria-controls="sidebarUser">
            <span class="nav-icon"><i class="fas fa-users"></i></span>
            <span class="nav-text">{{ t('menu.patient_records') }}</span>
        </a>
        <div class="{{ Route::is('admin.users.*') ? 'collapse show' : 'collapse' }}" id="sidebarUser">
            <ul class="nav sub-navbar-nav">
                <li class="sub-nav-item">
                    <a class="sub-nav-link" href="{{ route('admin.users.create') }}">{{ t('create') }}</a>
                </li>
                <li class="sub-nav-item">
                    <a class="sub-nav-link" href="{{ route('admin.users.index') }}">{{ t('list') }}</a>
                </li>
            </ul>
        </div>
    </li>
@endif
