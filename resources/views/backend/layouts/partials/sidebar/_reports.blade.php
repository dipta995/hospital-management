@if ($userGuard->can('reports.index'))
    @php
        $reportsActive = Route::is('admin.reports.*');
    @endphp
    @include('backend.layouts.partials.sidebar._section-title', [
        'title' => 'Reports & Analytics',
        'sectionClass' => 'section-reports',
        'sectionKey' => 'reports',
        'icon' => 'fa-chart-pie',
    ])
    <li class="nav-item" data-sidebar-section="reports">
        <a class="nav-link menu-arrow {{ $reportsActive ? 'active' : 'collapsed' }}"
           href="#sidebarReport" data-bs-toggle="collapse" role="button"
           aria-expanded="{{ $reportsActive ? 'true' : 'false' }}" aria-controls="sidebarReport">
            <span class="nav-icon"><i class="fas fa-chart-line"></i></span>
            <span class="nav-text">{{ __('language.reports') }}</span>
        </a>
        <div class="{{ $reportsActive ? 'collapse show' : 'collapse' }}" id="sidebarReport">
            <ul class="nav sub-navbar-nav sidebar-module-inner">

                @if ($userGuard->can('reports.show'))
                    <li class="sub-nav-section">Balance</li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.reports.balance') }}">Monthly Balance</a>
                    </li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.reports.balance-day-wise') }}">Day-wise Balance</a>
                    </li>
                @endif

                @if ($userGuard->can('reports.show'))
                    <li class="sub-nav-section">Collections</li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.reports.collections') }}">Diagnostic Collections</a>
                    </li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.reports.recept-collections') }}">Hospital Collections</a>
                    </li>
                @endif

                @if ($userGuard->can('reports.show') || $userGuard->can('reports.payment'))
                    <li class="sub-nav-section">Referrers & Sales</li>
                @endif
                @if ($userGuard->can('reports.payment'))
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.reports.categories') }}">Sales by Category</a>
                    </li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.reports.references.payment') }}">Refer Payment</a>
                    </li>
                @endif
                @if ($userGuard->can('reports.show'))
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.reports.references') }}">Refer Commission</a>
                    </li>
                @endif

                @if ($userGuard->can('reports.show'))
                    <li class="sub-nav-section">Other</li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.reports.costs') }}">Cost Report</a>
                    </li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.reports.pharmacy-stock') }}">Pharmacy Stock</a>
                    </li>
                @endif

            </ul>
        </div>
    </li>
@endif
