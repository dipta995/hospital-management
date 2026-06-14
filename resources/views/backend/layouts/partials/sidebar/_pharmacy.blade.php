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
        'title' => 'Pharmacy',
        'sectionClass' => 'section-pharmacy',
        'sectionKey' => 'pharmacy',
        'icon' => 'fa-pills',
    ])
    <li class="nav-item sidebar-module sidebar-module-pharmacy" data-sidebar-section="pharmacy">
        <a class="nav-link menu-arrow {{ $pharmacyActive ? 'active' : 'collapsed' }}"
           href="#sidebarPharmacyModule" data-bs-toggle="collapse" role="button"
           aria-expanded="{{ $pharmacyActive ? 'true' : 'false' }}" aria-controls="sidebarPharmacyModule">
            <span class="nav-icon"><i class="fas fa-prescription-bottle-alt"></i></span>
            <span class="nav-text">Pharmacy Module</span>
        </a>
        <div class="{{ $pharmacyActive ? 'collapse show' : 'collapse' }}" id="sidebarPharmacyModule">
            <ul class="nav sub-navbar-nav sidebar-module-inner">

                @if ($userGuard->can('pharmacy_sales.index') || $userGuard->can('pharmacy_sales.create'))
                    <li class="sub-nav-section">POS & Sales</li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.pharmacy_sales.index') }}">
                            <i class="fas fa-cash-register fa-fw me-1"></i> Sales List
                        </a>
                    </li>
                    @if ($userGuard->can('pharmacy_sales.create'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.pharmacy_sales.create') }}">+ New Sale</a>
                        </li>
                    @endif
                @endif

                @if ($userGuard->can('pharmacy_purchases.index') || $userGuard->can('pharmacy_purchases.create'))
                    <li class="sub-nav-section">Stock In</li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.pharmacy_purchases.index') }}">
                            <i class="fas fa-truck-loading fa-fw me-1"></i> Purchases
                        </a>
                    </li>
                    @if ($userGuard->can('pharmacy_purchases.create'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.pharmacy_purchases.create') }}">+ New Purchase</a>
                        </li>
                    @endif
                @endif

                @if ($userGuard->can('pharmacy_products.index') || $userGuard->can('pharmacy_products.create'))
                    <li class="sub-nav-section">Products</li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.pharmacy_products.index') }}">
                            <i class="fas fa-capsules fa-fw me-1"></i> Product List / Stock
                        </a>
                    </li>
                    @if ($userGuard->can('pharmacy_products.create'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.pharmacy_products.create') }}">+ New Product</a>
                        </li>
                    @endif
                @endif

                @if ($userGuard->can('pharmacy_categories.index') || $userGuard->can('pharmacy_brands.index')
                    || $userGuard->can('pharmacy_types.index') || $userGuard->can('pharmacy_units.index'))
                    <li class="sub-nav-section">Catalog Setup</li>
                @endif
                @if ($userGuard->can('pharmacy_categories.index') || $userGuard->can('pharmacy_categories.create'))
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.pharmacy_categories.index') }}">Categories</a>
                    </li>
                @endif
                @if ($userGuard->can('pharmacy_brands.index') || $userGuard->can('pharmacy_brands.create'))
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.pharmacy_brands.index') }}">Brands</a>
                    </li>
                @endif
                @if ($userGuard->can('pharmacy_types.index') || $userGuard->can('pharmacy_types.create'))
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.pharmacy_types.index') }}">Types</a>
                    </li>
                @endif
                @if ($userGuard->can('pharmacy_units.index') || $userGuard->can('pharmacy_units.create'))
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.pharmacy_units.index') }}">Units</a>
                    </li>
                @endif

            </ul>
        </div>
    </li>
@endif
