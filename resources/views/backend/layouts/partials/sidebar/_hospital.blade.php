@php
    $hasHospital = ($userGuard->can('admits.index') || $userGuard->can('admits.create'))
        || ($userGuard->can('service_categories.index') || $userGuard->can('service_categories.create'))
        || ($userGuard->can('services.index') || $userGuard->can('services.create'))
        || ($userGuard->can('recepts.index') || $userGuard->can('recepts.create'))
        || ($userGuard->can('bed_cabins.index') || $userGuard->can('bed_cabins.create'))
        || $userGuard->can('costs.index');
    $hospitalActive = Route::is('admin.admits.*', 'admin.service_categories.*', 'admin.services.*', 'admin.recepts.*', 'admin.bed_cabins.*', 'admin.hospital_costs.*');
@endphp
@if ($hasHospital)
    @include('backend.layouts.partials.sidebar._section-title', [
        'title' => 'Hospital / IPD',
        'sectionClass' => 'section-hospital',
        'sectionKey' => 'hospital',
        'icon' => 'fa-hospital',
    ])
    <li class="nav-item sidebar-module sidebar-module-hospital" data-sidebar-section="hospital">
        <a class="nav-link menu-arrow {{ $hospitalActive ? 'active' : 'collapsed' }}"
           href="#sidebarHospitalModule" data-bs-toggle="collapse" role="button"
           aria-expanded="{{ $hospitalActive ? 'true' : 'false' }}" aria-controls="sidebarHospitalModule">
            <span class="nav-icon"><i class="fas fa-procedures"></i></span>
            <span class="nav-text">Hospital Module</span>
        </a>
        <div class="{{ $hospitalActive ? 'collapse show' : 'collapse' }}" id="sidebarHospitalModule">
            <ul class="nav sub-navbar-nav sidebar-module-inner">

                @if ($userGuard->can('admits.index') || $userGuard->can('admits.create'))
                    <li class="sub-nav-section">Admission</li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.admits.index') }}">
                            <i class="fas fa-bed fa-fw me-1"></i> {{ __('Admit') }} List
                        </a>
                    </li>
                @endif

                @if ($userGuard->can('bed_cabins.index') || $userGuard->can('bed_cabins.create'))
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.bed_cabins.index') }}">
                            <i class="fas fa-door-closed fa-fw me-1"></i> Bed / Cabin
                        </a>
                    </li>
                    @if ($userGuard->can('bed_cabins.create'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.bed_cabins.create') }}">+ New Bed/Cabin</a>
                        </li>
                    @endif
                @endif

                @if ($userGuard->can('service_categories.index') || $userGuard->can('services.index'))
                    <li class="sub-nav-section">Services</li>
                @endif
                @if ($userGuard->can('service_categories.index') || $userGuard->can('service_categories.create'))
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.service_categories.index') }}">Service Categories</a>
                    </li>
                @endif
                @if ($userGuard->can('services.index') || $userGuard->can('services.create'))
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.services.index') }}">Services</a>
                    </li>
                @endif

                @if ($userGuard->can('recepts.index'))
                    <li class="sub-nav-section">Billing</li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.recepts.index') }}">
                            <i class="fas fa-receipt fa-fw me-1"></i> {{ __('Recept') }}
                        </a>
                    </li>
                @endif

                @if ($userGuard->can('costs.index'))
                    <li class="sub-nav-section">Expenses</li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.hospital_costs.index') }}">
                            <i class="fas fa-hospital fa-fw me-1"></i> Hospital Cost
                        </a>
                    </li>
                @endif

            </ul>
        </div>
    </li>
@endif
