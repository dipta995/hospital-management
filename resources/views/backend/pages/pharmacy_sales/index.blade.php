@extends('backend.layouts.master')

@section('title')
    {{ $pageHeader['title'] }}
@endsection

@push('styles')
    @include('backend.layouts.partials.crud-styles')
    @include('backend.layouts.partials.pharmacy-styles')
@endpush

@section('admin-content')
    @php $userGuard = Auth::guard('admin')->user(); $fmt = fn($n) => number_format((float)$n, 2); @endphp

    <div class="crud-page pharm-page container-fluid py-3">
        @include('backend.layouts.partials.crud-hero', [
            'heroTitle' => 'Pharmacy Sales',
            'heroSubtitle' => 'POS sales, invoices & due collection',
            'heroIcon' => 'fa-cash-register',
            'heroCreateRoute' => $userGuard->can('pharmacy_sales.create') ? $pageHeader['create_route'] : null,
            'heroCreateLabel' => 'New Sale',
        ])

        <div class="pharm-kpi-grid">
            <div class="pharm-kpi">
                <div class="pharm-kpi-label">Today's Sales</div>
                <div class="pharm-kpi-value">{{ $stats['today_count'] ?? 0 }}</div>
            </div>
            <div class="pharm-kpi">
                <div class="pharm-kpi-label">Today's Amount</div>
                <div class="pharm-kpi-value">৳ {{ $fmt($stats['today_amount'] ?? 0) }}</div>
            </div>
            <div class="pharm-kpi">
                <div class="pharm-kpi-label">Outstanding Due</div>
                <div class="pharm-kpi-value text-danger">৳ {{ $fmt($stats['total_due'] ?? 0) }}</div>
            </div>
            <div class="pharm-kpi">
                <div class="pharm-kpi-label">Due Invoices</div>
                <div class="pharm-kpi-value">{{ $stats['due_invoices'] ?? 0 }}</div>
            </div>
        </div>

        <div class="crud-card">
            @include('backend.layouts.partials.message')

            <form method="GET" class="crud-toolbar">
                <div class="row g-2 align-items-end flex-grow-1">
                    <div class="col-md-2">
                        <label class="form-label" for="start_date">From</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="end_date">To</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="sale_id">Sale ID</label>
                        <input type="text" name="sale_id" id="sale_id" class="form-control" value="{{ request('sale_id') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="phone">Customer Phone</label>
                        <input type="text" name="phone" id="phone" class="form-control" value="{{ request('phone') }}" placeholder="01XXXXXXXXX">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('admin.pharmacy_sales.index') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </div>
            </form>

            <div class="crud-table-wrap">
                <div class="table-responsive">
                    <table class="table crud-table table-hover mb-0">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Paid</th>
                            <th class="text-end">Due</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($datas as $sale)
                            <tr id="table-data{{ $sale->id }}">
                                <td><strong>#{{ $sale->id }}</strong></td>
                                <td>{{ $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') : '—' }}</td>
                                <td>{{ optional($sale->customer)->name ?? '—' }}</td>
                                <td>{{ optional($sale->customer)->phone ?? '—' }}</td>
                                <td class="text-end fw-semibold">৳ {{ $fmt($sale->total_amount) }}</td>
                                <td class="text-end">৳ {{ $fmt($sale->paid_amount) }}</td>
                                <td class="text-end">
                                    @if($sale->due_amount > 0)
                                        <span class="crud-badge pharm-stock-out">৳ {{ $fmt($sale->due_amount) }}</span>
                                    @else
                                        <span class="crud-badge pharm-stock-ok">Paid</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="crud-action-group justify-content-end">
                                        @if($userGuard->can('pharmacy_sales.edit'))
                                            <a href="{{ route($pageHeader['edit_route'], $sale->id) }}" class="crud-btn-icon crud-btn-edit" title="Edit"><i class="fas fa-pen"></i></a>
                                        @endif
                                        <a href="{{ route('admin.pharmacy_sales.pdf-preview', $sale->id) }}" class="crud-btn-icon crud-btn-view" target="_blank" title="Invoice"><i class="fas fa-file-invoice"></i></a>
                                        @if($sale->due_amount > 0 && $userGuard->can('pharmacy_sales.edit'))
                                            <button type="button" class="crud-btn-icon crud-btn-warning" title="Pay Due"
                                                    data-bs-toggle="modal" data-bs-target="#payDueModal{{ $sale->id }}"><i class="fas fa-money-bill"></i></button>
                                        @endif
                                        @if($userGuard->can('pharmacy_sales.delete'))
                                            <a href="javascript:void(0)" class="crud-btn-icon crud-btn-delete" title="Delete"
                                               onclick="pharmSaleDelete({{ $sale->id }}, '{{ $pageHeader['base_url'] }}')"><i class="fas fa-trash"></i></a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="crud-empty">No sales found. @if($userGuard->can('pharmacy_sales.create'))<a href="{{ route($pageHeader['create_route']) }}" class="btn btn-sm btn-primary ms-2">New Sale</a>@endif</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">{!! $datas->appends(request()->query())->links() !!}</div>
        </div>
    </div>

    @foreach($datas as $sale)
        @if($sale->due_amount > 0)
            <div class="modal fade crud-modal" id="payDueModal{{ $sale->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-sm">
                    <div class="modal-content">
                        <div class="modal-header"><h5 class="modal-title">Pay Due — Sale #{{ $sale->id }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                        <form action="{{ route('admin.pharmacy_sales.due-pay', $sale->id) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <p class="small text-muted mb-2">Due: <strong>৳ {{ $fmt($sale->due_amount) }}</strong></p>
                                <label class="form-label">Payment Amount</label>
                                <input type="number" name="paid_amount" step="0.01" min="0.01" max="{{ $sale->due_amount }}" class="form-control" value="{{ $sale->due_amount }}" required>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-crud-submit btn-sm">Record Payment</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection

@push('scripts')
<script>
    function pharmSaleDelete(id, baseUrl) {
        if (!confirm('Delete this sale? Stock will not auto-restore.')) return;
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = baseUrl + '/' + id;
        form.innerHTML = '@csrf @method("DELETE")';
        document.body.appendChild(form);
        form.submit();
    }
</script>
@endpush
