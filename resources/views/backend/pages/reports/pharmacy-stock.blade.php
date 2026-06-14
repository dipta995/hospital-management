@extends('backend.layouts.master')
@section('title')
    {{ $pageHeader['title'] }}
@endsection

@push('styles')
    @include('backend.layouts.partials.invoice-styles')
    @include('backend.layouts.partials.crud-styles')
    @include('backend.layouts.partials.pharmacy-styles')
@endpush

@section('admin-content')
    @php
        $fmt = fn($n) => number_format((float)$n, 0);
        $totalProducts = $datas->count();
        $outOfStock = $datas->filter(fn($p) => $p->current_stock <= 0)->count();
        $lowStock = $datas->filter(fn($p) => $p->current_stock > 0 && $p->current_stock <= $p->alert_qty)->count();
        $totalStock = $datas->sum(fn($p) => max($p->current_stock, 0));
    @endphp

    <div class="crud-page pharm-page inv-page container-fluid py-3">
        @include('backend.layouts.partials.report-hero', [
            'reportTitle' => 'Pharmacy Stock Report',
            'reportSubtitle' => 'Purchased vs sold · current stock by product',
            'reportIcon' => 'fa-warehouse',
            'resetRoute' => route('admin.reports.pharmacy-stock'),
        ])

        <div class="pharm-kpi-grid">
            <div class="pharm-kpi">
                <div class="pharm-kpi-label">Products</div>
                <div class="pharm-kpi-value">{{ $totalProducts }}</div>
            </div>
            <div class="pharm-kpi">
                <div class="pharm-kpi-label">Total Stock Units</div>
                <div class="pharm-kpi-value">{{ $fmt($totalStock) }}</div>
            </div>
            <div class="pharm-kpi">
                <div class="pharm-kpi-label">Low Stock</div>
                <div class="pharm-kpi-value text-warning">{{ $lowStock }}</div>
            </div>
            <div class="pharm-kpi">
                <div class="pharm-kpi-label">Out of Stock</div>
                <div class="pharm-kpi-value text-danger">{{ $outOfStock }}</div>
            </div>
        </div>

        <div class="crud-card">
            @include('backend.layouts.partials.message')

            <form method="GET" class="crud-toolbar mb-0 border-bottom-0 pb-0">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Quick filter (client)</label>
                        <input type="text" id="stock-search" class="form-control" placeholder="Search product, generic, category...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select id="stock-status-filter" class="form-select">
                            <option value="">All</option>
                            <option value="ok">In stock</option>
                            <option value="low">Low stock</option>
                            <option value="out">Out of stock</option>
                        </select>
                    </div>
                    <div class="col-md-5 text-md-end">
                        <a href="{{ route('admin.pharmacy_purchases.create') }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-truck-loading"></i> New Purchase</a>
                        <a href="{{ route('admin.pharmacy_products.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-pills"></i> Products</a>
                    </div>
                </div>
            </form>

            <div class="crud-table-wrap">
                <div class="table-responsive">
                    <table class="table crud-table table-hover mb-0" id="stock-report-table">
                        <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th>Unit</th>
                            <th class="text-end">Purchased</th>
                            <th class="text-end">Sold</th>
                            <th class="text-end">Current Stock</th>
                            <th class="text-end">Alert</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($datas as $product)
                            @php
                                $current = (float) $product->current_stock;
                                $alert = (float) $product->alert_qty;
                                if ($current <= 0) {
                                    $statusKey = 'out';
                                    $statusClass = 'pharm-stock-out';
                                    $statusLabel = 'Out';
                                } elseif ($current <= $alert) {
                                    $statusKey = 'low';
                                    $statusClass = 'pharm-stock-low';
                                    $statusLabel = 'Low';
                                } else {
                                    $statusKey = 'ok';
                                    $statusClass = 'pharm-stock-ok';
                                    $statusLabel = 'OK';
                                }
                            @endphp
                            <tr data-status="{{ $statusKey }}"
                                data-search="{{ strtolower($product->name . ' ' . ($product->generic_name ?? '') . ' ' . optional($product->category)->name) }}">
                                <td>
                                    <strong>{{ $product->name }}</strong>
                                    @if($product->generic_name)
                                        <div class="small text-muted">{{ $product->generic_name }}</div>
                                    @endif
                                </td>
                                <td>{{ optional($product->category)->name ?? '—' }}</td>
                                <td>{{ optional($product->brand)->name ?? '—' }}</td>
                                <td>{{ optional($product->quantityType)->name ?? '—' }}</td>
                                <td class="text-end">{{ $fmt($product->total_purchased) }}</td>
                                <td class="text-end">{{ $fmt($product->total_sold) }}</td>
                                <td class="text-end fw-semibold">{{ $fmt(max($current, 0)) }}</td>
                                <td class="text-end">{{ $fmt($alert) }}</td>
                                <td><span class="crud-badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="crud-empty">No pharmacy products found.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function filterStockTable() {
        const q = ($('#stock-search').val() || '').toLowerCase().trim();
        const status = $('#stock-status-filter').val();
        $('#stock-report-table tbody tr').each(function () {
            const matchSearch = !q || ($(this).data('search') || '').includes(q);
            const matchStatus = !status || $(this).data('status') === status;
            $(this).toggle(matchSearch && matchStatus);
        });
    }
    $('#stock-search, #stock-status-filter').on('input change', filterStockTable);
</script>
@endpush
