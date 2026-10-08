@php
    $securityLinks = array_filter([
        'report' => canSecurity('security.report', $userGuard),
        'logins' => canSecurity('security.logins', $userGuard),
        'settings' => canSecurity('security.settings', $userGuard),
    ]);
    $securityCashierLinks = $userGuard->can('invoices.index');
@endphp
@if (!empty($securityLinks) || $securityCashierLinks)
    @include('backend.layouts.partials.sidebar._section-title', [
        'title' => 'Security',
        'sectionClass' => 'section-admin',
        'sectionKey' => 'security',
        'icon' => 'fa-user-shield',
    ])

    @if ($securityCashierLinks)
        <li class="nav-item {{ Route::is('admin.cash-closings.*') ? 'active' : '' }}" data-sidebar-section="security">
            <a class="nav-link" href="{{ route('admin.cash-closings.index') }}">
                <span class="nav-icon"><i class="fas fa-cash-register"></i></span>
                <span class="nav-text">Cash Closing</span>
            </a>
        </li>
        <li class="nav-item {{ Route::is('admin.invoice-cancel-requests.*') ? 'active' : '' }}" data-sidebar-section="security">
            <a class="nav-link" href="{{ route('admin.invoice-cancel-requests.index') }}">
                <span class="nav-icon"><i class="fas fa-ban"></i></span>
                <span class="nav-text">Cancel Requests</span>
            </a>
        </li>
    @endif

    @isset($securityLinks['report'])
        <li class="nav-item {{ Route::is('admin.security.report') ? 'active' : '' }}" data-sidebar-section="security">
            <a class="nav-link" href="{{ route('admin.security.report') }}">
                <span class="nav-icon"><i class="fas fa-user-secret"></i></span>
                <span class="nav-text">Suspicious Report</span>
            </a>
        </li>
    @endisset
    @isset($securityLinks['logins'])
        <li class="nav-item {{ Route::is('admin.security.logins') ? 'active' : '' }}" data-sidebar-section="security">
            <a class="nav-link" href="{{ route('admin.security.logins') }}">
                <span class="nav-icon"><i class="fas fa-right-to-bracket"></i></span>
                <span class="nav-text">Login History</span>
            </a>
        </li>
    @endisset
    @isset($securityLinks['settings'])
        <li class="nav-item {{ Route::is('admin.security.settings') ? 'active' : '' }}" data-sidebar-section="security">
            <a class="nav-link" href="{{ route('admin.security.settings') }}">
                <span class="nav-icon"><i class="fas fa-user-shield"></i></span>
                <span class="nav-text">Security Settings</span>
            </a>
        </li>
    @endisset
@endif
