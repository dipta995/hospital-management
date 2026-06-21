@php
    $topbarQuickLinksMode = $topbarQuickLinksMode ?? 'desktop';

    if (!isset($topbarQuickLinks)) {
        $topbarQuickLinks = [];

        if ($userGuard->can('invoices.index') || $userGuard->can('invoices.create') || $userGuard->can('invoices.edit') || $userGuard->can('invoices.delete')) {
            $topbarQuickLinks = array_merge($topbarQuickLinks, [
                ['label' => t('menu.patients'), 'mobile' => t('menu.patients'), 'route' => 'admin.users.index', 'icon' => 'fa-user-injured', 'tone' => 'patients'],
                ['label' => tp('Invoices'), 'mobile' => tp('Invoices'), 'route' => 'admin.invoices.index', 'icon' => 'fa-file-invoice-dollar', 'tone' => 'invoices'],
                ['label' => t('menu.admits'), 'mobile' => t('menu.admits'), 'route' => 'admin.admits.index', 'icon' => 'fa-bed', 'tone' => 'admits'],
                ['label' => t('menu.recepts'), 'mobile' => t('menu.recepts'), 'route' => 'admin.recepts.index', 'icon' => 'fa-receipt', 'tone' => 'recepts'],
            ]);
        }

        if ($userGuard->can('labs.index') || $userGuard->can('labs.create') || $userGuard->can('labs.edit') || $userGuard->can('labs.delete')) {
            $topbarQuickLinks[] = ['label' => t('menu.my_lab'), 'mobile' => t('lab'), 'route' => 'admin.labs.index', 'icon' => 'fa-flask', 'tone' => 'lab'];
        }

        if ($userGuard->can('costs.index') || $userGuard->can('costs.create') || $userGuard->can('costs.edit') || $userGuard->can('costs.delete')) {
            $topbarQuickLinks[] = ['label' => t('menu.add_cost'), 'mobile' => t('cost'), 'route' => 'admin.costs.create', 'icon' => 'fa-coins', 'tone' => 'cost'];
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
    @if($topbarQuickLinksMode === 'desktop')
        <nav class="tb-quick-nav d-none d-lg-flex" aria-label="{{ t('common.quick_navigation') }}">
            @foreach($topbarQuickLinks as $link)
                <a href="{{ route($link['route']) }}"
                   class="tb-quick-link tb-quick-link--{{ $link['tone'] }}{{ $isQuickLinkActive($link['route']) ? ' is-active' : '' }}">
                    <span class="tb-quick-link-icon"><i class="fas {{ $link['icon'] }}"></i></span>
                    <span class="tb-quick-link-text">{{ $link['label'] }}</span>
                </a>
            @endforeach
        </nav>
    @else
        <nav class="tb-quick-strip d-lg-none" aria-label="{{ t('common.quick_links') }}">
            <div class="tb-quick-strip-scroll">
                @foreach($topbarQuickLinks as $link)
                    <a href="{{ route($link['route']) }}"
                       class="tb-quick-chip tb-quick-chip--{{ $link['tone'] }}{{ $isQuickLinkActive($link['route']) ? ' is-active' : '' }}">
                        <i class="fas {{ $link['icon'] }}"></i>
                        <span>{{ $link['mobile'] ?? $link['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </nav>
    @endif
@endif
