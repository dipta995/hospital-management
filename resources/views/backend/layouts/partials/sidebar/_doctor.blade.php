@php
    $hasDoctor = ($userGuard->can('reefers.index') || $userGuard->can('reefers.create'))
        || ($userGuard->can('doctor_serials.index') || $userGuard->can('doctor_serials.create'))
        || ($userGuard->can('doctor_rooms.index') || $userGuard->can('doctor_rooms.create'))
        || ($userGuard->can('prescriptions.index') || $userGuard->can('prescriptions.create'));
    $doctorActive = Route::is('admin.reefers.*', 'admin.doctor_serials.*', 'admin.doctor_rooms.*', 'admin.prescriptions.*', 'admin.reports.references.doctor');
@endphp
@if ($hasDoctor)
    @include('backend.layouts.partials.sidebar._section-title', [
        'title' => 'Doctors & OPD',
        'sectionClass' => 'section-doctor',
        'sectionKey' => 'doctor',
        'icon' => 'fa-user-md',
    ])
    <li class="nav-item sidebar-module sidebar-module-doctor" data-sidebar-section="doctor">
        <a class="nav-link menu-arrow {{ $doctorActive ? 'active' : 'collapsed' }}"
           href="#sidebarDoctorModule" data-bs-toggle="collapse" role="button"
           aria-expanded="{{ $doctorActive ? 'true' : 'false' }}" aria-controls="sidebarDoctorModule">
            <span class="nav-icon"><i class="fas fa-stethoscope"></i></span>
            <span class="nav-text">Doctor Module</span>
        </a>
        <div class="{{ $doctorActive ? 'collapse show' : 'collapse' }}" id="sidebarDoctorModule">
            <ul class="nav sub-navbar-nav sidebar-module-inner">

                @if ($userGuard->can('reefers.index') || $userGuard->can('reefers.create'))
                    <li class="sub-nav-section">Referrers</li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.reefers.index') }}">
                            <i class="fas fa-user-tie fa-fw me-1"></i> {{ __('language.dr_refer') }}
                        </a>
                    </li>
                    @if ($userGuard->can('reefers.create'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.reefers.create') }}">+ New Referrer</a>
                        </li>
                    @endif
                    @if ($userGuard->can('reefers.index'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.reefers.custom-sms') }}">
                                <i class="fas fa-sms fa-fw me-1"></i> SMS Alert
                            </a>
                        </li>
                    @endif
                @endif

                @if ($userGuard->can('doctor_serials.index') || $userGuard->can('doctor_serials.create'))
                    <li class="sub-nav-section">OPD Queue</li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.doctor_serials.index') }}">
                            <i class="fas fa-list-ol fa-fw me-1"></i> Doctor Serials
                        </a>
                    </li>
                    @if ($userGuard->can('doctor_serials.create'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.doctor_serials.create') }}">+ New Serial</a>
                        </li>
                    @endif
                @endif

                @if ($userGuard->can('doctor_rooms.index') || $userGuard->can('doctor_rooms.create'))
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.doctor_rooms.index') }}">
                            <i class="fas fa-door-open fa-fw me-1"></i> Doctor Rooms
                        </a>
                    </li>
                @endif

                @if ($userGuard->can('prescriptions.index') || $userGuard->can('prescriptions.create'))
                    <li class="sub-nav-section">Prescriptions</li>
                    @if ($userGuard->can('prescriptions.create'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.prescriptions.create') }}">
                                <i class="fas fa-prescription fa-fw me-1"></i> Create Prescription
                            </a>
                        </li>
                    @endif
                    @if ($userGuard->can('prescriptions.index'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.prescriptions.index') }}">Prescription List</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.reports.references.doctor') }}">My Earning</a>
                        </li>
                    @endif
                @endif

            </ul>
        </div>
    </li>
@endif
