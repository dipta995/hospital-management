@php
    $userGuard = $userGuard ?? Auth::guard('admin')->user();

    if (!isset($topbarQuickLinks)) {
        $topbarQuickLinks = [];

        if ($userGuard->can('invoices.index') || $userGuard->can('invoices.create') || $userGuard->can('invoices.edit') || $userGuard->can('invoices.delete')) {
            $topbarQuickLinks = array_merge($topbarQuickLinks, [
                ['label' => t('menu.patients'), 'route' => 'admin.users.index', 'icon' => 'fa-user-injured', 'tone' => 'patients'],
                ['label' => tp('Invoices'), 'route' => 'admin.invoices.index', 'icon' => 'fa-file-invoice-dollar', 'tone' => 'invoices'],
                ['label' => t('menu.admits'), 'route' => 'admin.admits.index', 'icon' => 'fa-bed', 'tone' => 'admits'],
                ['label' => t('menu.recepts'), 'route' => 'admin.recepts.index', 'icon' => 'fa-receipt', 'tone' => 'recepts'],
            ]);
        }

        if ($userGuard->can('labs.index') || $userGuard->can('labs.create') || $userGuard->can('labs.edit') || $userGuard->can('labs.delete')) {
            $topbarQuickLinks[] = ['label' => t('menu.my_lab'), 'route' => 'admin.labs.index', 'icon' => 'fa-flask', 'tone' => 'lab'];
        }

        if ($userGuard->can('costs.index') || $userGuard->can('costs.create') || $userGuard->can('costs.edit') || $userGuard->can('costs.delete')) {
            $topbarQuickLinks[] = ['label' => t('menu.add_cost'), 'route' => 'admin.costs.create', 'icon' => 'fa-coins', 'tone' => 'cost'];
        }
    }

    $isQuickLinkActive = function (string $routeName): bool {
        if (request()->routeIs($routeName)) {
            return true;
        }

        $parts = explode('.', $routeName);
        if (count($parts) >= 2) {
            return request()->routeIs($parts[0] . '.' . $parts[1] . '.*');
        }

        return false;
    };
@endphp

@if(count($topbarQuickLinks))
    <div class="tb-quick-bar" data-tb-quick-bar>
        <button type="button" class="tb-quick-scroll tb-quick-scroll--prev" data-tb-quick-prev aria-label="{{ t('common.scroll_left') }}" hidden>
            <i class="fas fa-chevron-left"></i>
        </button>

        <nav class="tb-quick-bar-track" data-tb-quick-track aria-label="{{ t('common.quick_navigation') }}">
            <div class="tb-quick-bar-inner">
                @foreach($topbarQuickLinks as $link)
                    <a href="{{ route($link['route']) }}"
                       class="tb-quick-item tb-quick-item--{{ $link['tone'] }}{{ $isQuickLinkActive($link['route']) ? ' is-active' : '' }}"
                       title="{{ $link['label'] }}">
                        <span class="tb-quick-item-icon"><i class="fas {{ $link['icon'] }}"></i></span>
                        <span class="tb-quick-item-label">{{ $link['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </nav>

        <button type="button" class="tb-quick-scroll tb-quick-scroll--next" data-tb-quick-next aria-label="{{ t('common.scroll_right') }}" hidden>
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>
@endif
