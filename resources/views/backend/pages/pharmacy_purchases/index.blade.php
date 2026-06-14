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
            'heroTitle' => 'Pharmacy Purchases',
            'heroSubtitle' => 'Stock in from suppliers · payment tracking',
            'heroIcon' => 'fa-truck-loading',
            'heroCreateRoute' => $userGuard->can('pharmacy_purchases.create') ? $pageHeader['create_route'] : null,
            'heroCreateLabel' => 'New Purchase',
        ])

        <div class="pharm-kpi-grid">
            <div class="pharm-kpi">
                <div class="pharm-kpi-label">Total Purchases</div>
                <div class="pharm-kpi-value">{{ $stats['total_purchases'] ?? 0 }}</div>
            </div>
            <div class="pharm-kpi">
                <div class="pharm-kpi-label">This Month</div>
                <div class="pharm-kpi-value">{{ $stats['this_month'] ?? 0 }}</div>
            </div>
            <div class="pharm-kpi">
                <div class="pharm-kpi-label">Total Value</div>
                <div class="pharm-kpi-value">৳ {{ $fmt($stats['total_cost'] ?? 0) }}</div>
            </div>
            <div class="pharm-kpi">
                <div class="pharm-kpi-label">Outstanding Due</div>
                <div class="pharm-kpi-value text-danger">৳ {{ $fmt($stats['total_due'] ?? 0) }}</div>
            </div>
        </div>

        <div class="crud-card">
            @include('backend.layouts.partials.message')

            <div class="crud-table-wrap">
                <div class="table-responsive">
                    <table class="table crud-table table-hover mb-0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Supplier</th>
                            <th>Date</th>
                            <th class="text-center">Items</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Paid</th>
                            <th class="text-end">Due</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($datas as $item)
                            <tr id="table-data{{ $item->id }}">
                                <td>{{ $loop->iteration + ($datas->currentPage()-1)*$datas->perPage() }}</td>
                                <td><strong>{{ optional($item->supplier)->name ?? '—' }}</strong></td>
                                <td>{{ $item->purchase_date ? \Carbon\Carbon::parse($item->purchase_date)->format('d M Y') : '—' }}</td>
                                <td class="text-center"><span class="crud-badge pharm">{{ $item->items_count ?? $item->items->count() }}</span></td>
                                <td class="text-end fw-semibold">৳ {{ $fmt($item->total_cost) }}</td>
                                <td class="text-end">৳ {{ $fmt($item->paid_amount) }}</td>
                                <td class="text-end">
                                    @if($item->due_amount > 0)
                                        <span class="crud-badge pharm-stock-out">৳ {{ $fmt($item->due_amount) }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->status === 'Paid' || $item->due_amount <= 0)
                                        <span class="crud-badge pharm-stock-ok">Paid</span>
                                    @else
                                        <span class="crud-badge pharm-stock-low">{{ $item->status ?? 'Due' }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="crud-action-group justify-content-end">
                                        @if($userGuard->can('pharmacy_purchases.edit'))
                                            <a href="{{ route($pageHeader['edit_route'], $item->id) }}" class="crud-btn-icon crud-btn-edit" title="Edit"><i class="fas fa-pen"></i></a>
                                        @endif
                                        @if($userGuard->can('pharmacy_purchases.delete'))
                                            <button type="button" class="crud-btn-icon crud-btn-delete border-0 delete-purchase" data-id="{{ $item->id }}" title="Delete"><i class="fas fa-trash"></i></button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="crud-empty">No purchases found. <a href="{{ route($pageHeader['create_route']) }}">Create first purchase</a></td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="d-flex justify-content-end mt-3">{!! $datas->links() !!}</div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).on('click', '.delete-purchase', function () {
        if (!confirm('Delete this purchase? This cannot be undone if products were already sold.')) return;
        const id = $(this).data('id');
        $.ajax({
            url: '{{ route('admin.pharmacy_purchases.destroy', ':id') }}'.replace(':id', id),
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function (res) {
                if (res.status === 200) {
                    location.reload();
                } else {
                    alert(res.message || 'Failed to delete purchase.');
                }
            },
            error: function (xhr) {
                alert(xhr.responseJSON?.message || 'Something went wrong.');
            }
        });
    });
</script>
@endpush
