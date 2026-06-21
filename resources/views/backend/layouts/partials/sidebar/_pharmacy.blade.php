@php
    $hasPharmacy = ($userGuard->can('pharmacy_sales.index') || $userGuard->can('pharmacy_sales.create'))
        || ($userGuard->can('pharmacy_purchases.index') || $userGuard->can('pharmacy_purchases.create'))
        || ($userGuard->can('pharmacy_products.index') || $userGuard->can('pharmacy_products.create'))
        || ($userGuard->can('pharmacy_categories.index') || $userGuard->can('pharmacy_brands.index')
            || $userGuard->can('pharmacy_types.index') || $userGuard->can('pharmacy_units.index'));
    $pharmacyActive = Route::is(
        'admin.pharmacy_categories.*',
        'admin.pharmacy_brands.*',
        'admin.pharmacy_types.*',
        'admin.pharmacy_units.*',
        'admin.pharmacy_products.*',
        'admin.pharmacy_purchases.*',
        'admin.pharmacy_sales.*'
    );
@endphp
@if ($hasPharmacy)
    @include('backend.layouts.partials.sidebar._section-title', [
        'title' => t('menu.pharmacy'),
        'sectionClass' => 'section-pharmacy',
        'sectionKey' => 'pharmacy',
        'icon' => 'fa-pills',
    ])
    <li class="nav-item sidebar-module sidebar-module-pharmacy" data-sidebar-section="pharmacy">
        <a class="nav-link menu-arrow {{ $pharmacyActive ? 'active' : 'collapsed' }}"
           href="#sidebarPharmacyModule" data-bs-toggle="collapse" role="button"
           aria-expanded="{{ $pharmacyActive ? 'true' : 'false' }}" aria-controls="sidebarPharmacyModule">
            <span class="nav-icon"><i class="fas fa-prescription-bottle-alt"></i></span>
            <span class="nav-text">{{ t('menu.pharmacy_module') }}</span>
        </a>
        <div class="{{ $pharmacyActive ? 'collapse show' : 'collapse' }}" id="sidebarPharmacyModule">
            <ul class="nav sub-navbar-nav sidebar-module-inner">

                @if ($userGuard->can('pharmacy_sales.index') || $userGuard->can('pharmacy_sales.create'))
                    <li class="sub-nav-section">{{ t('menu.pos_sales') }}</li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.pharmacy_sales.index') }}">
                            <i class="fas fa-cash-register fa-fw me-1"></i> {{ t('menu.sales_list') }}
                        </a>
                    </li>
                    @if ($userGuard->can('pharmacy_sales.create'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.pharmacy_sales.create') }}">+ {{ t('menu.new_sale') }}</a>
                        </li>
                    @endif
                @endif

                @if ($userGuard->can('pharmacy_purchases.index') || $userGuard->can('pharmacy_purchases.create'))
                    <li class="sub-nav-section">{{ t('menu.stock_in') }}</li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.pharmacy_purchases.index') }}">
                            <i class="fas fa-truck-loading fa-fw me-1"></i> {{ t('menu.purchases') }}
                        </a>
                    </li>
                    @if ($userGuard->can('pharmacy_purchases.create'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.pharmacy_purchases.create') }}">+ {{ t('menu.new_purchase') }}</a>
                        </li>
                    @endif
                @endif

                @if ($userGuard->can('pharmacy_products.index') || $userGuard->can('pharmacy_products.create'))
                    <li class="sub-nav-section">{{ t('menu.products') }}</li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.pharmacy_products.index') }}">
                            <i class="fas fa-capsules fa-fw me-1"></i> {{ t('menu.product_list_stock') }}
                        </a>
                    </li>
                    @if ($userGuard->can('pharmacy_products.create'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.pharmacy_products.create') }}">+ {{ t('menu.new_product') }}</a>
                        </li>
                    @endif
                @endif

                @if ($userGuard->can('pharmacy_categories.index') || $userGuard->can('pharmacy_brands.index')
                    || $userGuard->can('pharmacy_types.index') || $userGuard->can('pharmacy_units.index'))
                    <li class="sub-nav-section">{{ t('menu.catalog_setup') }}</li>
                @endif
                @if ($userGuard->can('pharmacy_categories.index') || $userGuard->can('pharmacy_categories.create'))
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.pharmacy_categories.index') }}">{{ t('menu.categories') }}</a>
                    </li>
                @endif
                @if ($userGuard->can('pharmacy_brands.index') || $userGuard->can('pharmacy_brands.create'))
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.pharmacy_brands.index') }}">{{ t('menu.brands') }}</a>
                    </li>
                @endif
                @if ($userGuard->can('pharmacy_types.index') || $userGuard->can('pharmacy_types.create'))
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.pharmacy_types.index') }}">{{ t('menu.types') }}</a>
                    </li>
                @endif
                @if ($userGuard->can('pharmacy_units.index') || $userGuard->can('pharmacy_units.create'))
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.pharmacy_units.index') }}">{{ t('menu.units') }}</a>
                    </li>
                @endif

            </ul>
        </div>
    </li>
@endif
