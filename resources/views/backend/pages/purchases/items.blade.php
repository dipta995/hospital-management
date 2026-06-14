@extends('backend.layouts.master')

@section('title')
    Purchase Stock Items
@endsection

@push('styles')
    @include('backend.layouts.partials.crud-styles')
    @include('backend.layouts.partials.pharmacy-styles')
    <style>
        .purchase-page .crud-hero { background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%); color: #fff; }
        .purchase-page .pharm-kpi-value { font-size: 1.25rem; }
        .stock-badge-ok { background: #ecfdf5; color: #047857; }
        .stock-badge-low { background: #fffbeb; color: #b45309; }
        .stock-badge-out { background: #fef2f2; color: #b91c1c; }
        .stock-badge-none { background: #f1f5f9; color: #64748b; }
        .expiry-badge-expired { background: #fef2f2; color: #b91c1c; }
        .expiry-badge-soon { background: #fffbeb; color: #b45309; }
        .expiry-badge-ok { background: #ecfdf5; color: #047857; }
        .expiry-badge-none { background: #f1f5f9; color: #64748b; }
    </style>
@endpush

@section('admin-content')
    @php
        $userGuard = Auth::guard('admin')->user();
        $fmt = fn($n) => number_format((float)$n, 0);
    @endphp

    <div class="crud-page purchase-page container-fluid py-3">
        @include('backend.layouts.partials.crud-hero', [
            'heroTitle' => 'Purchase Stock Items',
            'heroSubtitle' => 'Batch-wise inventory · expiry & usage tracking',
            'heroIcon' => 'fa-boxes-stacked',
            'heroCreateRoute' => $userGuard->can('purchases.create') ? 'admin.purchases.create' : null,
            'heroCreateLabel' => 'New Purchase',
        ])

        <div class="pharm-kpi-grid">
            <div class="pharm-kpi">
                <div class="pharm-kpi-label">Total Batches</div>
                <div class="pharm-kpi-value">{{ $stats['total_lines'] ?? 0 }}</div>
            </div>
            <div class="pharm-kpi">
                <div class="pharm-kpi-label">In Stock</div>
                <div class="pharm-kpi-value text-success">{{ $stats['in_stock'] ?? 0 }}</div>
            </div>
            <div class="pharm-kpi">
                <div class="pharm-kpi-label">Expired (in stock)</div>
                <div class="pharm-kpi-value text-danger">{{ $stats['expired'] ?? 0 }}</div>
            </div>
            <div class="pharm-kpi">
                <div class="pharm-kpi-label">Expiring ≤ 7 days</div>
                <div class="pharm-kpi-value text-warning">{{ $stats['expiring_soon'] ?? 0 }}</div>
            </div>
        </div>

        <div class="crud-card">
            @include('backend.layouts.partials.message')

            <form method="GET" class="crud-toolbar">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Item, code, supplier...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Stock</label>
                        <select name="stock" class="form-select">
                            <option value="in_stock" @selected(request('stock', 'in_stock') === 'in_stock')>In stock</option>
                            <option value="depleted" @selected(request('stock') === 'depleted')>Depleted</option>
                            <option value="all" @selected(request('stock') === 'all')>All batches</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Expiry</label>
                        <select name="expiry" class="form-select">
                            <option value="all" @selected(request('expiry', 'all') === 'all')>All</option>
                            <option value="soon" @selected(request('expiry') === 'soon')>Expiring soon</option>
                            <option value="expired" @selected(request('expiry') === 'expired')>Expired</option>
                            <option value="unexpired" @selected(request('expiry') === 'unexpired')>Not expired</option>
                            <option value="low" @selected(request('expiry') === 'low')>Low stock (≥90% used)</option>
                        </select>
                    </div>
                    <div class="col-md-5 d-flex gap-2 flex-wrap">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('admin.items.purchases') }}" class="btn btn-outline-secondary">Reset</a>
                        <a href="{{ route('admin.purchases.index') }}" class="btn btn-outline-primary ms-md-auto"><i class="fas fa-list"></i> Purchases</a>
                    </div>
                </div>
            </form>

            <div class="crud-table-wrap">
                <div class="table-responsive">
                    <table class="table crud-table table-hover mb-0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Supplier</th>
                            <th>Purchase</th>
                            <th class="text-end">Purchased</th>
                            <th class="text-end">Used</th>
                            <th class="text-end">Remaining</th>
                            <th>Usage</th>
                            <th>Expiry</th>
                            <th class="text-end">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($datas as $key => $row)
                            @php
                                $remaining = $row->remaining ?? max((int)$row->quantity - (int)$row->quantity_spend, 0);
                                $usedPct = $row->used_pct ?? 0;
                                $stockClass = $remaining <= 0 ? 'stock-badge-out' : ($usedPct >= 90 ? 'stock-badge-low' : 'stock-badge-ok');

                                $expiryBadge = 'expiry-badge-none';
                                $expiryLabel = '—';
                                if ($row->expiry_date) {
                                    $expiry = \Carbon\Carbon::parse($row->expiry_date);
                                    $expiryLabel = $expiry->format('d M Y');
                                    if ($expiry->isPast()) {
                                        $expiryBadge = 'expiry-badge-expired';
                                    } elseif ($expiry->lte(now()->addDays(7))) {
                                        $expiryBadge = 'expiry-badge-soon';
                                    } else {
                                        $expiryBadge = 'expiry-badge-ok';
                                    }
                                }
                            @endphp
                            <tr>
                                <td>{{ $datas->firstItem() + $key }}</td>
                                <td>
                                    <strong>{{ optional($row->item)->name ?? '—' }}</strong>
                                    @if(optional($row->item)->code)
                                        <div class="small"><code>{{ $row->item->code }}</code></div>
                                    @endif
                                </td>
                                <td>{{ optional($row->supplier)->name ?? '—' }}</td>
                                <td>
                                    @if($row->purchase)
                                        <a href="{{ route('admin.purchases.show', $row->purchase_id) }}" class="text-decoration-none">
                                            #{{ $row->purchase_id }}
                                        </a>
                                        <div class="small text-muted">{{ $row->purchase->purchase_date ? \Carbon\Carbon::parse($row->purchase->purchase_date)->format('d M Y') : '' }}</div>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="text-end">{{ $fmt($row->quantity) }}</td>
                                <td class="text-end text-muted">{{ $fmt($row->quantity_spend) }}</td>
                                <td class="text-end">
                                    <span class="crud-badge {{ $stockClass }}">{{ $fmt($remaining) }}</span>
                                </td>
                                <td>
                                    <div class="progress" style="height:8px; min-width:80px;">
                                        <div class="progress-bar {{ $usedPct >= 90 ? 'bg-danger' : ($usedPct >= 70 ? 'bg-warning' : 'bg-success') }}"
                                            style="width: {{ min($usedPct, 100) }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ $usedPct }}%</small>
                                </td>
                                <td>
                                    <span class="crud-badge {{ $expiryBadge }}">{{ $expiryLabel }}</span>
                                </td>
                                <td class="text-end">
                                    @if($userGuard->can('purchases.edit'))
                                        <a href="{{ route('admin.purchases.edit-item', $row->id) }}" class="crud-btn-icon crud-btn-edit" title="Adjust stock">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="crud-empty">
                                    No stock batches found.
                                    @if($userGuard->can('purchases.create'))
                                        <a href="{{ route('admin.purchases.create') }}">Create a purchase</a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $datas->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection
