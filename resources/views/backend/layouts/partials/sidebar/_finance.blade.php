@php
    $hasFinance = ($userGuard->can('earns.index') || $userGuard->can('earns.create'))
        || ($userGuard->can('cost_categories.index') || $userGuard->can('cost_categories.create'))
        || ($userGuard->can('costs.index') || $userGuard->can('costs.create'));
    $financeActive = Route::is('admin.earns.*', 'admin.cost_categories.*', 'admin.costs.*');
@endphp
@if ($hasFinance)
    @include('backend.layouts.partials.sidebar._section-title', [
        'title' => t('menu.finance'),
        'sectionClass' => 'section-finance',
        'sectionKey' => 'finance',
        'icon' => 'fa-coins',
    ])
    <li class="nav-item sidebar-module sidebar-module-finance" data-sidebar-section="finance">
        <a class="nav-link menu-arrow {{ $financeActive ? 'active' : 'collapsed' }}"
           href="#sidebarFinanceModule" data-bs-toggle="collapse" role="button"
           aria-expanded="{{ $financeActive ? 'true' : 'false' }}" aria-controls="sidebarFinanceModule">
            <span class="nav-icon"><i class="fas fa-wallet"></i></span>
            <span class="nav-text">{{ t('menu.finance_module') }}</span>
        </a>
        <div class="{{ $financeActive ? 'collapse show' : 'collapse' }}" id="sidebarFinanceModule">
            <ul class="nav sub-navbar-nav sidebar-module-inner">

                @if ($userGuard->can('earns.index') || $userGuard->can('earns.create'))
                    <li class="sub-nav-section">{{ t('menu.income') }}</li>
                    @if ($userGuard->can('earns.create'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.earns.create') }}">
                                <i class="fas fa-plus-circle fa-fw me-1"></i> {{ t('menu.new_earn') }}
                            </a>
                        </li>
                    @endif
                    @if ($userGuard->can('earns.index'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.earns.index') }}">{{ t('menu.earn_list') }}</a>
                        </li>
                    @endif
                @endif

                @if ($userGuard->can('cost_categories.index') || $userGuard->can('costs.index'))
                    <li class="sub-nav-section">{{ t('menu.expenses_diagnostic') }}</li>
                @endif
                @if ($userGuard->can('cost_categories.index') || $userGuard->can('cost_categories.create'))
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.cost_categories.index') }}">
                            {{ t('cost') }} {{ t('category') }}
                        </a>
                    </li>
                @endif
                @if ($userGuard->can('costs.index') || $userGuard->can('costs.create'))
                    @if ($userGuard->can('costs.create'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.costs.create') }}">+ {{ t('menu.new_cost') }}</a>
                        </li>
                    @endif
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.costs.index') }}">{{ t('menu.cost_list') }}</a>
                    </li>
                @endif

            </ul>
        </div>
    </li>
@endif
