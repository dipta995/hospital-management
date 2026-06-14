@extends('backend.layouts.master')

@section('title')
    {{ $pageHeader['title'] }}
@endsection

@push('styles')
    @include('backend.layouts.partials.crud-styles')
@endpush

@section('admin-content')
    @php
        $userGuard = Auth::guard('admin')->user();
        $fmt = fn ($n) => number_format((float) $n, 2);
    @endphp

    <div class="crud-page container-fluid py-3">
        @include('backend.layouts.partials.crud-hero', [
            'heroTitle' => 'Inventory Purchases',
            'heroSubtitle' => 'General stock purchases from suppliers',
            'heroIcon' => 'fa-dolly',
            'heroCreateRoute' => $userGuard->can('purchases.create') ? $pageHeader['create_route'] : null,
            'heroCreateLabel' => 'New Purchase',
        ])

        <div class="crud-card">
            @include('backend.layouts.partials.message')

            <div class="crud-table-wrap">
                <div class="table-responsive">
                    <table class="table crud-table table-hover mb-0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Supplier</th>
                            <th>Purchase Date</th>
                            <th class="text-end">Total Cost</th>
                            <th class="text-end">Paid</th>
                            <th class="text-end">Due</th>
                            <th class="text-end">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($datas as $item)
                            @php $due = max(0, (float) $item->total_cost - (float) ($item->purchase_paid_sum_amount ?? 0)); @endphp
                            <tr id="table-data{{ $item->id }}">
                                <td>{{ $loop->iteration + ($datas->currentPage() - 1) * $datas->perPage() }}</td>
                                <td><strong>{{ optional($item->supplier)->name ?? '—' }}</strong></td>
                                <td>{{ $item->purchase_date ? \Carbon\Carbon::parse($item->purchase_date)->format('d M Y') : '—' }}</td>
                                <td class="text-end fw-semibold">৳ {{ $fmt($item->total_cost) }}</td>
                                <td class="text-end">৳ {{ $fmt($item->purchase_paid_sum_amount ?? 0) }}</td>
                                <td class="text-end">
                                    @if($due > 0)
                                        <span class="crud-badge bg-danger-subtle text-danger">৳ {{ $fmt($due) }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="crud-action-group justify-content-end">
                                        <a href="{{ route('admin.purchases.show', $item->id) }}" class="crud-btn-icon crud-btn-view" title="View"><i class="fas fa-eye"></i></a>
                                        @if($userGuard->can('purchases.edit'))
                                            <a href="{{ route($pageHeader['edit_route'], $item->id) }}" class="crud-btn-icon crud-btn-edit" title="Edit"><i class="fas fa-pen"></i></a>
                                        @endif
                                        @if($userGuard->can('purchases.delete'))
                                            <button type="button" class="crud-btn-icon crud-btn-delete border-0" title="Delete"
                                                    onclick="dataDelete({{ $item->id }}, '{{ $pageHeader['base_url'] }}')"><i class="fas fa-trash"></i></button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="crud-empty">No purchases found. <a href="{{ route($pageHeader['create_route']) }}">Create purchase</a></td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="crud-pagination">{!! $datas->links() !!}</div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function dataDelete(id, baseUrl) {
        if (confirm('Are you sure you want to delete this purchase?')) {
            $.ajax({
                url: baseUrl + '/' + id,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function (response) {
                    if (response.status === 200) {
                        $('#table-data' + id).remove();
                    } else {
                        alert('Delete failed.');
                    }
                },
                error: function () { alert('Something went wrong!'); }
            });
        }
    }
</script>
@endpush
