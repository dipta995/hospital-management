@extends('backend.layouts.master')

@section('title')
    Purchase #{{ $purchase->id }}
@endsection

@push('styles')
    @include('backend.layouts.partials.crud-styles')
@endpush

@section('admin-content')
    @php
        $fmt = fn ($n) => number_format((float) $n, 2);
        $paid = (float) ($purchase->purchase_paid_sum_amount ?? 0);
        $due = max(0, (float) $purchase->total_cost - $paid);
    @endphp

    <div class="crud-page container-fluid py-3">
        @include('backend.layouts.partials.crud-hero', [
            'heroTitle' => 'Purchase #'.$purchase->id,
            'heroSubtitle' => optional($purchase->supplier)->name ?? 'Supplier purchase details',
            'heroIcon' => 'fa-dolly',
            'heroCreateRoute' => null,
        ])

        <div class="d-flex gap-2 mb-3 flex-wrap">
            <a href="{{ route($pageHeader['index_route']) }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
            @if($due > 0)
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#referPaymentModal">
                    <i class="fas fa-money-bill-wave"></i> Pay Due (৳ {{ $fmt($due) }})
                </button>
            @endif
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-3"><div class="crud-card p-3"><div class="small text-muted">Total Cost</div><div class="fs-4 fw-bold">৳ {{ $fmt($purchase->total_cost) }}</div></div></div>
            <div class="col-md-3"><div class="crud-card p-3"><div class="small text-muted">Paid</div><div class="fs-4 fw-bold text-success">৳ {{ $fmt($paid) }}</div></div></div>
            <div class="col-md-3"><div class="crud-card p-3"><div class="small text-muted">Due</div><div class="fs-4 fw-bold {{ $due > 0 ? 'text-danger' : '' }}">৳ {{ $fmt($due) }}</div></div></div>
            <div class="col-md-3"><div class="crud-card p-3"><div class="small text-muted">Date</div><div class="fs-5 fw-bold">{{ $purchase->purchase_date ? \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') : '—' }}</div></div></div>
        </div>

        <div class="crud-card mb-3">
            <div class="p-3 border-bottom"><h6 class="mb-0 fw-bold">Purchased Items</h6></div>
            <div class="crud-table-wrap">
                <div class="table-responsive">
                    <table class="table crud-table mb-0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Supplier</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Left</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Discount</th>
                            <th>Expiry</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($purchase->purchaseItems as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ optional($item->item)->name ?? '—' }}</td>
                                <td>{{ optional($item->supplier)->name ?? '—' }}</td>
                                <td class="text-end">{{ $item->quantity }}</td>
                                <td class="text-end">{{ $item->quantity_spend }}</td>
                                <td class="text-end">৳ {{ $fmt($item->unit_price) }}</td>
                                <td class="text-end">৳ {{ $fmt($item->discount_amount) }}</td>
                                <td>{{ $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date)->format('d M Y') : '—' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="referPaymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="{{ route('admin.purchases.payment') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Purchase Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Supplier</label>
                            <input type="text" class="form-control" value="{{ $purchase->supplier->name ?? '' }}" readonly>
                            <input type="hidden" name="purchase_id" value="{{ $purchase->id }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <input type="number" class="form-control" name="amount" value="{{ $due }}" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Account No</label>
                            <input type="text" class="form-control" name="account_no">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" name="date" value="{{ now()->toDateString() }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select class="form-select" name="payment_type" required>
                                <option value="" disabled selected>Select method</option>
                                @foreach(\App\Models\Payment::$paymentStatusArray as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Pay</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
