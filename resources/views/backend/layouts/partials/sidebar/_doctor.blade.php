@php
    $hasDoctor = ($userGuard->can('reefers.index') || $userGuard->can('reefers.create'))
        || ($userGuard->can('doctor_serials.index') || $userGuard->can('doctor_serials.create'))
        || ($userGuard->can('doctor_rooms.index') || $userGuard->can('doctor_rooms.create'))
        || ($userGuard->can('prescriptions.index') || $userGuard->can('prescriptions.create'));
    $doctorActive = Route::is('admin.reefers.*', 'admin.doctor_serials.*', 'admin.doctor_rooms.*', 'admin.prescriptions.*', 'admin.reports.references.doctor');
@endphp
@if ($hasDoctor)
    @include('backend.layouts.partials.sidebar._section-title', [
        'title' => t('menu.doctors_opd'),
        'sectionClass' => 'section-doctor',
        'sectionKey' => 'doctor',
        'icon' => 'fa-user-md',
    ])
    <li class="nav-item sidebar-module sidebar-module-doctor" data-sidebar-section="doctor">
        <a class="nav-link menu-arrow {{ $doctorActive ? 'active' : 'collapsed' }}"
           href="#sidebarDoctorModule" data-bs-toggle="collapse" role="button"
           aria-expanded="{{ $doctorActive ? 'true' : 'false' }}" aria-controls="sidebarDoctorModule">
            <span class="nav-icon"><i class="fas fa-stethoscope"></i></span>
            <span class="nav-text">{{ t('menu.doctor_module') }}</span>
        </a>
        <div class="{{ $doctorActive ? 'collapse show' : 'collapse' }}" id="sidebarDoctorModule">
            <ul class="nav sub-navbar-nav sidebar-module-inner">

                @if ($userGuard->can('reefers.index') || $userGuard->can('reefers.create'))
                    <li class="sub-nav-section">{{ t('menu.referrers') }}</li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.reefers.index') }}">
                            <i class="fas fa-user-tie fa-fw me-1"></i> {{ t('dr_refer') }}
                        </a>
                    </li>
                    @if ($userGuard->can('reefers.create'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.reefers.create') }}">+ {{ t('menu.new_referrer') }}</a>
                        </li>
                    @endif
                    @if ($userGuard->can('reefers.index'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.reefers.custom-sms') }}">
                                <i class="fas fa-sms fa-fw me-1"></i> {{ t('menu.sms_alert') }}
                            </a>
                        </li>
                    @endif
                @endif

                @if ($userGuard->can('doctor_serials.index') || $userGuard->can('doctor_serials.create'))
                    <li class="sub-nav-section">{{ t('menu.opd_queue') }}</li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.doctor_serials.index') }}">
                            <i class="fas fa-list-ol fa-fw me-1"></i> {{ t('menu.doctor_serials') }}
                        </a>
                    </li>
                    @if ($userGuard->can('doctor_serials.create'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.doctor_serials.create') }}">+ {{ t('menu.new_serial') }}</a>
                        </li>
                    @endif
                @endif

                @if ($userGuard->can('doctor_rooms.index') || $userGuard->can('doctor_rooms.create'))
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.doctor_rooms.index') }}">
                            <i class="fas fa-door-open fa-fw me-1"></i> {{ t('menu.doctor_rooms') }}
                        </a>
                    </li>
                @endif

                @if ($userGuard->can('prescriptions.index') || $userGuard->can('prescriptions.create'))
                    <li class="sub-nav-section">{{ t('menu.prescriptions') }}</li>
                    @if ($userGuard->can('prescriptions.create'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.prescriptions.create') }}">
                                <i class="fas fa-prescription fa-fw me-1"></i> {{ t('menu.create_prescription') }}
                            </a>
                        </li>
                    @endif
                    @if ($userGuard->can('prescriptions.index'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.prescriptions.index') }}">{{ t('menu.prescription_list') }}</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.reports.references.doctor') }}">{{ t('menu.my_earning') }}</a>
                        </li>
                    @endif
                @endif

            </ul>
        </div>
    </li>
@endif
