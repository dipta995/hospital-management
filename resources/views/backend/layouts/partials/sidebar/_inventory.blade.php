@php
    $hasInventory = ($userGuard->can('suppliers.index') || $userGuard->can('suppliers.create'))
        || ($userGuard->can('items.index') || $userGuard->can('items.create'))
        || ($userGuard->can('purchases.index') || $userGuard->can('purchases.create'));
    $inventoryActive = Route::is('admin.suppliers.*', 'admin.items.*', 'admin.purchases.*', 'admin.items.purchases');
@endphp
@if ($hasInventory)
    @include('backend.layouts.partials.sidebar._section-title', [
        'title' => t('menu.general_inventory'),
        'sectionClass' => 'section-inventory',
        'sectionKey' => 'inventory',
        'icon' => 'fa-boxes',
    ])
    <li class="nav-item sidebar-module sidebar-module-inventory" data-sidebar-section="inventory">
        <a class="nav-link menu-arrow {{ $inventoryActive ? 'active' : 'collapsed' }}"
           href="#sidebarInventoryModule" data-bs-toggle="collapse" role="button"
           aria-expanded="{{ $inventoryActive ? 'true' : 'false' }}" aria-controls="sidebarInventoryModule">
            <span class="nav-icon"><i class="fas fa-warehouse"></i></span>
            <span class="nav-text">{{ t('menu.inventory_module') }}</span>
        </a>
        <div class="{{ $inventoryActive ? 'collapse show' : 'collapse' }}" id="sidebarInventoryModule">
            <ul class="nav sub-navbar-nav sidebar-module-inner">

                @if ($userGuard->can('suppliers.index') || $userGuard->can('suppliers.create'))
                    <li class="sub-nav-section">{{ t('menu.suppliers') }}</li>
                    @if ($userGuard->can('suppliers.create'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.suppliers.create') }}">+ {{ t('menu.new_supplier') }}</a>
                        </li>
                    @endif
                    @if ($userGuard->can('suppliers.index'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.suppliers.index') }}">{{ t('menu.supplier_list') }}</a>
                        </li>
                    @endif
                @endif

                @if ($userGuard->can('items.index') || $userGuard->can('items.create'))
                    <li class="sub-nav-section">{{ t('menu.items') }}</li>
                    @if ($userGuard->can('items.create'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.items.create') }}">+ {{ t('menu.new_item') }}</a>
                        </li>
                    @endif
                    @if ($userGuard->can('items.index'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.items.index') }}">{{ t('menu.item_list') }}</a>
                        </li>
                    @endif
                @endif

                @if ($userGuard->can('purchases.index') || $userGuard->can('purchases.create'))
                    <li class="sub-nav-section">{{ t('menu.purchasing_stock') }}</li>
                    @if ($userGuard->can('purchases.create'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.purchases.create') }}">+ {{ t('menu.new_purchase') }}</a>
                        </li>
                    @endif
                    @if ($userGuard->can('purchases.index'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.purchases.index') }}">{{ t('menu.purchase_list') }}</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.items.purchases') }}">
                                <i class="fas fa-boxes fa-fw me-1"></i> {{ t('menu.stock_items') }}
                            </a>
                        </li>
                    @endif
                @endif

            </ul>
        </div>
    </li>
@endif
