@php
    $hasHr = ($userGuard->can('employees.index') || $userGuard->can('employees.create'))
        || ($userGuard->can('employees.index') || $userGuard->can('settings.edit'));
@endphp
@if ($hasHr)
    @include('backend.layouts.partials.sidebar._section-title', [
        'title' => 'HR & Payroll',
        'sectionClass' => 'section-hr',
        'sectionKey' => 'hr',
        'icon' => 'fa-id-badge',
    ])
    <li class="nav-item sidebar-module sidebar-module-hr" data-sidebar-section="hr">
        <a class="nav-link menu-arrow {{ Route::is('admin.employees.*', 'admin.attendance.*') ? 'active' : 'collapsed' }}"
           href="#sidebarHrModule" data-bs-toggle="collapse" role="button"
           aria-expanded="{{ Route::is('admin.employees.*', 'admin.attendance.*') ? 'true' : 'false' }}" aria-controls="sidebarHrModule">
            <span class="nav-icon"><i class="fas fa-users-cog"></i></span>
            <span class="nav-text">HR Module</span>
        </a>
        <div class="{{ Route::is('admin.employees.*', 'admin.attendance.*') ? 'collapse show' : 'collapse' }}" id="sidebarHrModule">
            <ul class="nav sub-navbar-nav sidebar-module-inner">

                @if ($userGuard->can('employees.index') || $userGuard->can('employees.create'))
                    <li class="sub-nav-section">Employees</li>
                    @if ($userGuard->can('employees.create'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.employees.create') }}">+ New Employee</a>
                        </li>
                    @endif
                    @if ($userGuard->can('employees.index'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.employees.index') }}">{{ __('language.employee') }} List</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.employees.salary-sheet') }}">
                                <i class="fas fa-file-invoice-dollar fa-fw me-1"></i> Salary Sheet
                            </a>
                        </li>
                    @endif
                @endif

                @if ($userGuard->can('employees.index') || $userGuard->can('settings.edit'))
                    <li class="sub-nav-section">Attendance</li>
                    @if ($userGuard->can('employees.index'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.attendance.index') }}">Summary</a>
                        </li>
                    @endif
                    @if ($userGuard->can('settings.edit'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.settings.edit', auth()->id()) }}#attendance-config">Settings</a>
                        </li>
                    @endif
                @endif

            </ul>
        </div>
    </li>
@endif
