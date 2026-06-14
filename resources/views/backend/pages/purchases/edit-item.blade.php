@extends('backend.layouts.master')

@section('title')
    Edit Stock Item
@endsection

@push('styles')
    @include('backend.layouts.partials.crud-styles')
    @include('backend.layouts.partials.pharmacy-styles')
    <style>
        .purchase-page .crud-hero { background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%); color: #fff; }
        .purchase-page .btn-crud-submit { background: linear-gradient(135deg, #0f766e, #14b8a6); }
    </style>
@endpush

@section('admin-content')
    @php
        $remaining = max((int) $edited->quantity - (int) $edited->quantity_spend, 0);
        $usedPct = $edited->quantity > 0 ? (int) round($edited->quantity_spend / $edited->quantity * 100) : 0;
    @endphp

    <div class="crud-page purchase-page container-fluid py-3">
        @include('backend.layouts.partials.crud-form-hero', [
            'formTitle' => 'Adjust Stock Batch',
            'formSubtitle' => optional($edited->item)->name ?? 'Purchase item #' . $edited->id,
            'formIcon' => 'fa-boxes-stacked',
        ])

        <div class="crud-card">
            @include('backend.layouts.partials.message')

            <div class="crud-form-section">
                <div class="crud-form-section-header"><i class="fas fa-info-circle"></i> Batch Info</div>
                <div class="crud-form-section-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="pharm-kpi">
                                <div class="pharm-kpi-label">Supplier</div>
                                <div class="pharm-kpi-value" style="font-size:1rem">{{ optional($edited->supplier)->name ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="pharm-kpi">
                                <div class="pharm-kpi-label">Purchase</div>
                                <div class="pharm-kpi-value" style="font-size:1rem">
                                    @if($edited->purchase)
                                        <a href="{{ route('admin.purchases.show', $edited->purchase_id) }}">#{{ $edited->purchase_id }}</a>
                                    @else — @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="pharm-kpi">
                                <div class="pharm-kpi-label">Expiry</div>
                                <div class="pharm-kpi-value" style="font-size:1rem">
                                    {{ $edited->expiry_date ? \Carbon\Carbon::parse($edited->expiry_date)->format('d M Y') : '—' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="pharm-kpi">
                                <div class="pharm-kpi-label">Current Remaining</div>
                                <div class="pharm-kpi-value text-success" style="font-size:1rem">{{ $remaining }} ({{ $usedPct }}% used)</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form method="post" action="{{ route('admin.purchases.update-item', $edited->id) }}">
                @csrf
                <div class="crud-form-section">
                    <div class="crud-form-section-header"><i class="fas fa-sliders-h"></i> Adjust Quantities</div>
                    <div class="crud-form-section-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Total Purchased Qty <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" min="0" class="form-control"
                                    value="{{ old('quantity', $edited->quantity) }}" required id="qty_total">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Quantity Used / Spent <span class="text-danger">*</span></label>
                                <input type="number" name="quantity_spend" min="0" class="form-control"
                                    value="{{ old('quantity_spend', $edited->quantity_spend) }}" required id="qty_spend">
                                <small class="text-muted">Cannot exceed total purchased qty.</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Remaining (preview)</label>
                                <input type="text" class="form-control bg-light" readonly id="qty_remaining"
                                    value="{{ $remaining }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="crud-form-actions">
                    <a href="{{ route('admin.items.purchases') }}" class="btn-crud-cancel">Back to Stock</a>
                    <button type="submit" class="btn btn-crud-submit"><i class="fas fa-save"></i> Update Stock</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function updateRemaining() {
        const total = parseInt($('#qty_total').val()) || 0;
        const spent = parseInt($('#qty_spend').val()) || 0;
        $('#qty_remaining').val(Math.max(total - spent, 0));
        $('#qty_spend').attr('max', total);
    }
    $('#qty_total, #qty_spend').on('input', updateRemaining);
</script>
@endpush
