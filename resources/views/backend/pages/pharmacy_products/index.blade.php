@extends('backend.layouts.master')
@section('title')
    {{ $pageHeader['title'] }}
@endsection

@push('styles')
    @include('backend.layouts.partials.crud-styles')
    @include('backend.layouts.partials.pharmacy-styles')
@endpush

@section('admin-content')
    @php
        $userGuard = Auth::guard('admin')->user();
        $fmt = fn($n) => number_format((float)$n, 2);
    @endphp

    <div class="crud-page pharm-page container-fluid py-3">
        @include('backend.layouts.partials.crud-hero', [
            'heroTitle' => 'Pharmacy Products & Stock',
            'heroSubtitle' => 'Catalog, pricing and live stock levels',
            'heroIcon' => 'fa-pills',
            'heroCreateRoute' => $userGuard->can('pharmacy_products.create') ? $pageHeader['create_route'] : null,
            'heroCreateLabel' => 'Add Product',
        ])

        <div class="pharm-kpi-grid">
            <div class="pharm-kpi">
                <div class="pharm-kpi-label">Products</div>
                <div class="pharm-kpi-value">{{ $stats['total_products'] ?? 0 }}</div>
            </div>
            <div class="pharm-kpi">
                <div class="pharm-kpi-label">Total Stock Units</div>
                <div class="pharm-kpi-value">{{ number_format($stats['total_stock_units'] ?? 0) }}</div>
            </div>
            <div class="pharm-kpi">
                <div class="pharm-kpi-label">Low Stock</div>
                <div class="pharm-kpi-value text-warning">{{ $stats['low_stock'] ?? 0 }}</div>
            </div>
            <div class="pharm-kpi">
                <div class="pharm-kpi-label">Out of Stock</div>
                <div class="pharm-kpi-value text-danger">{{ $stats['out_of_stock'] ?? 0 }}</div>
            </div>
        </div>

        <div class="crud-card">
            @include('backend.layouts.partials.message')

            <form method="GET" class="crud-toolbar">
                <div class="row g-2 align-items-end flex-grow-1">
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Name, generic, barcode...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Stock Filter</label>
                        <select name="stock" class="form-select">
                            <option value="">All</option>
                            <option value="low" @selected(request('stock') === 'low')>Low stock</option>
                            <option value="out" @selected(request('stock') === 'out')>Out of stock</option>
                        </select>
                    </div>
                    <div class="col-md-5 d-flex gap-2 flex-wrap">
                        <button type="submit" class="btn btn-primary">Search</button>
                        <a href="{{ route('admin.pharmacy_products.index') }}" class="btn btn-outline-secondary">Reset</a>
                        @if($userGuard->can('pharmacy_purchases.create'))
                            <a href="{{ route('admin.pharmacy_purchases.create') }}" class="btn btn-outline-primary ms-md-auto"><i class="fas fa-truck-loading"></i> Purchase Stock</a>
                        @endif
                        @if($userGuard->can('reports.index'))
                            <a href="{{ route('admin.reports.pharmacy-stock') }}" class="btn btn-outline-primary"><i class="fas fa-warehouse"></i> Stock Report</a>
                        @endif
                    </div>
                </div>
            </form>

            <div class="crud-table-wrap">
                <div class="table-responsive">
                    <table class="table crud-table table-hover mb-0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th class="text-end">Purchase</th>
                            <th class="text-end">Sell</th>
                            <th class="text-end">In</th>
                            <th class="text-end">Out</th>
                            <th class="text-end">Stock</th>
                            <th class="text-end">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($datas as $key => $data)
                            @php
                                $stock = $data->stock_qty ?? 0;
                                $stockClass = $stock <= 0 ? 'pharm-stock-out' : ($stock <= $data->alert_qty ? 'pharm-stock-low' : 'pharm-stock-ok');
                            @endphp
                            <tr>
                                <td>{{ $datas->firstItem() + $key }}</td>
                                <td>
                                    <strong>{{ $data->name }}</strong>
                                    @if($data->generic_name)<div class="small text-muted">{{ $data->generic_name }} @if($data->strength)· {{ $data->strength }}@endif</div>@endif
                                    @if($data->barcode)<div class="small"><code>{{ $data->barcode }}</code></div>@endif
                                </td>
                                <td>{{ optional($data->category)->name ?? '—' }}</td>
                                <td class="text-end text-muted">৳ {{ $fmt($data->purchase_price) }}</td>
                                <td class="text-end fw-semibold">৳ {{ $fmt($data->sell_price) }}</td>
                                <td class="text-end text-muted">{{ number_format($data->purchased_qty ?? 0) }}</td>
                                <td class="text-end text-muted">{{ number_format($data->sold_qty ?? 0) }}</td>
                                <td class="text-end">
                                    <span class="crud-badge {{ $stockClass }}">{{ number_format($stock) }} {{ optional($data->quantityType)->name }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="crud-action-group justify-content-end">
                                        @if($userGuard->can('pharmacy_products.edit'))
                                            <a href="{{ route('admin.pharmacy_products.edit', $data->id) }}" class="crud-btn-icon crud-btn-edit" title="Edit"><i class="fas fa-pen"></i></a>
                                        @endif
                                        @if($userGuard->can('pharmacy_products.delete'))
                                            <button type="button" class="crud-btn-icon crud-btn-delete border-0 delete-item" data-id="{{ $data->id }}" title="Delete"><i class="fas fa-trash"></i></button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="crud-empty">No products found.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="d-flex justify-content-end mt-3">{{ $datas->withQueryString()->links() }}</div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).on('click', '.delete-item', function () {
        if (!confirm('Delete this product? Products with purchase/sale history cannot be deleted.')) return;
        $.ajax({
            url: '{{ route('admin.pharmacy_products.destroy', ':id') }}'.replace(':id', $(this).data('id')),
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function (res) {
                if (res.status === 200) location.reload();
                else alert(res.message || 'Failed to delete.');
            },
            error: function (xhr) {
                alert(xhr.responseJSON?.message || 'Something went wrong.');
            }
        });
    });
</script>
@endpush
