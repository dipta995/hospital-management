@if ($userGuard->can('reports.index'))
    @php
        $reportsActive = Route::is('admin.reports.*');
    @endphp
    @include('backend.layouts.partials.sidebar._section-title', [
        'title' => t('menu.reports_analytics'),
        'sectionClass' => 'section-reports',
        'sectionKey' => 'reports',
        'icon' => 'fa-chart-pie',
    ])
    <li class="nav-item" data-sidebar-section="reports">
        <a class="nav-link menu-arrow {{ $reportsActive ? 'active' : 'collapsed' }}"
           href="#sidebarReport" data-bs-toggle="collapse" role="button"
           aria-expanded="{{ $reportsActive ? 'true' : 'false' }}" aria-controls="sidebarReport">
            <span class="nav-icon"><i class="fas fa-chart-line"></i></span>
            <span class="nav-text">{{ t('reports') }}</span>
        </a>
        <div class="{{ $reportsActive ? 'collapse show' : 'collapse' }}" id="sidebarReport">
            <ul class="nav sub-navbar-nav sidebar-module-inner">

                @if ($userGuard->can('reports.show'))
                    <li class="sub-nav-section">{{ t('menu.balance_section') }}</li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.reports.balance') }}">{{ t('menu.monthly_balance') }}</a>
                    </li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.reports.balance-day-wise') }}">{{ t('menu.day_wise_balance') }}</a>
                    </li>
                @endif

                @if ($userGuard->can('reports.show'))
                    <li class="sub-nav-section">{{ t('menu.collections') }}</li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.reports.collections') }}">{{ t('menu.diagnostic_collections') }}</a>
                    </li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.reports.recept-collections') }}">{{ t('menu.hospital_collections') }}</a>
                    </li>
                @endif

                @if ($userGuard->can('reports.show') || $userGuard->can('reports.payment'))
                    <li class="sub-nav-section">{{ t('menu.referrers_sales') }}</li>
                @endif
                @if ($userGuard->can('reports.payment'))
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.reports.categories') }}">{{ t('menu.sales_by_category') }}</a>
                    </li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.reports.references.payment') }}">{{ t('menu.refer_payment') }}</a>
                    </li>
                @endif
                @if ($userGuard->can('reports.show'))
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.reports.references') }}">{{ t('menu.refer_commission') }}</a>
                    </li>
                @endif

                @if ($userGuard->can('reports.show'))
                    <li class="sub-nav-section">{{ t('common.other') }}</li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.reports.costs') }}">{{ t('menu.cost_report') }}</a>
                    </li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.reports.pharmacy-stock') }}">{{ t('menu.pharmacy_stock') }}</a>
                    </li>
                @endif

            </ul>
        </div>
    </li>
@endif
