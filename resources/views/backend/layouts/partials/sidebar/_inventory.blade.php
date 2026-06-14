@php
    $hasInventory = ($userGuard->can('suppliers.index') || $userGuard->can('suppliers.create'))
        || ($userGuard->can('items.index') || $userGuard->can('items.create'))
        || ($userGuard->can('purchases.index') || $userGuard->can('purchases.create'));
    $inventoryActive = Route::is('admin.suppliers.*', 'admin.items.*', 'admin.purchases.*', 'admin.items.purchases');
@endphp
@if ($hasInventory)
    @include('backend.layouts.partials.sidebar._section-title', [
        'title' => 'General Inventory',
        'sectionClass' => 'section-inventory',
        'sectionKey' => 'inventory',
        'icon' => 'fa-boxes',
    ])
    <li class="nav-item sidebar-module sidebar-module-inventory" data-sidebar-section="inventory">
        <a class="nav-link menu-arrow {{ $inventoryActive ? 'active' : 'collapsed' }}"
           href="#sidebarInventoryModule" data-bs-toggle="collapse" role="button"
           aria-expanded="{{ $inventoryActive ? 'true' : 'false' }}" aria-controls="sidebarInventoryModule">
            <span class="nav-icon"><i class="fas fa-warehouse"></i></span>
            <span class="nav-text">Inventory Module</span>
        </a>
        <div class="{{ $inventoryActive ? 'collapse show' : 'collapse' }}" id="sidebarInventoryModule">
            <ul class="nav sub-navbar-nav sidebar-module-inner">

                @if ($userGuard->can('suppliers.index') || $userGuard->can('suppliers.create'))
                    <li class="sub-nav-section">Suppliers</li>
                    @if ($userGuard->can('suppliers.create'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.suppliers.create') }}">+ New Supplier</a>
                        </li>
                    @endif
                    @if ($userGuard->can('suppliers.index'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.suppliers.index') }}">Supplier List</a>
                        </li>
                    @endif
                @endif

                @if ($userGuard->can('items.index') || $userGuard->can('items.create'))
                    <li class="sub-nav-section">Items</li>
                    @if ($userGuard->can('items.create'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.items.create') }}">+ New Item</a>
                        </li>
                    @endif
                    @if ($userGuard->can('items.index'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.items.index') }}">Item List</a>
                        </li>
                    @endif
                @endif

                @if ($userGuard->can('purchases.index') || $userGuard->can('purchases.create'))
                    <li class="sub-nav-section">Purchasing & Stock</li>
                    @if ($userGuard->can('purchases.create'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.purchases.create') }}">+ New Purchase</a>
                        </li>
                    @endif
                    @if ($userGuard->can('purchases.index'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.purchases.index') }}">Purchase List</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.items.purchases') }}">
                                <i class="fas fa-boxes fa-fw me-1"></i> Stock Items
                            </a>
                        </li>
                    @endif
                @endif

            </ul>
        </div>
    </li>
@endif
