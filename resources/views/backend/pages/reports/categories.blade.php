@extends('backend.layouts.master')
@section('title')
    {{ $pageHeader['title'] }}
@endsection
@push('styles')
    @include('backend.layouts.partials.report-styles')
@endpush
@section('admin-content')
    @php
        $fmt = fn ($n) => number_format((float) $n, 2);
        $grandTotal = 0;
        foreach ($datas as $cat) {
            $grandTotal += ($cat['total_price'] ?? 0) - ($cat['discount_price'] ?? 0);
        }
        $periodLabel = request('start_date') || request('end_date')
            ? (request('start_date') ?: '…') . ' → ' . (request('end_date') ?: '…')
            : 'Today (' . now()->format('d M Y') . ')';
    @endphp

    <div class="inv-page container-fluid py-3">
        @include('backend.layouts.partials.report-hero', [
            'reportTitle' => 'Sales by Category',
            'reportSubtitle' => 'Diagnostic tests/products grouped by category',
            'reportIcon' => 'fa-layer-group',
            'resetRoute' => route('admin.reports.categories'),
        ])

        @include('backend.layouts.partials.message')

        <div class="inv-kpi-grid">
            <div class="inv-kpi">
                <div class="inv-kpi-icon collection"><i class="fas fa-folder"></i></div>
                <div>
                    <div class="inv-kpi-label">Categories</div>
                    <div class="inv-kpi-value">{{ count($datas) }}</div>
                </div>
            </div>
            <div class="inv-kpi">
                <div class="inv-kpi-icon"><i class="fas fa-coins"></i></div>
                <div>
                    <div class="inv-kpi-label">Net Total (after discount)</div>
                    <div class="inv-kpi-value">৳ {{ $fmt($grandTotal) }}</div>
                </div>
            </div>
            <div class="inv-kpi">
                <div class="inv-kpi-icon"><i class="fas fa-calendar"></i></div>
                <div>
                    <div class="inv-kpi-label">Period</div>
                    <div class="inv-kpi-value" style="font-size:0.9rem">{{ $periodLabel }}</div>
                </div>
            </div>
        </div>

        <div class="inv-panel mb-3">
            <form action="" method="GET" class="inv-filter-toolbar">
                <div class="filter-field">
                    <label class="form-label">Category</label>
                    <select class="form-select" name="category_id">
                        <option value="">All categories</option>
                        @foreach($categories as $item)
                            <option @selected(request('category_id') == $item->id) value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-field">
                    <label class="form-label">From</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="filter-field">
                    <label class="form-label">To</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="filter-field">
                    <label class="form-label">Doctor</label>
                    <select class="form-select report-select2" name="dr_refer_id">
                        <option value="">All doctors</option>
                        @foreach($reffers as $item)
                            <option @selected(request('dr_refer_id') == $item->id) value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-field" style="max-width:120px">
                    <label class="form-label">PDF</label>
                    <select class="form-select" name="export">
                        <option value="">No</option>
                        <option value="pdf">Export</option>
                    </select>
                </div>
                <div class="inv-filter-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Apply</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table inv-table mb-0">
                    <thead>
                    <tr>
                        <th>Category</th>
                        <th>Product / Test</th>
                        <th>Invoice</th>
                        <th>Doctor</th>
                        <th class="text-end">After Discount</th>
                        <th>Date</th>
                        <th class="text-end">List Price</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($datas as $categoryName => $categoryData)
                        <tr class="report-date-row">
                            <td colspan="7">
                                <i class="fas fa-folder-open me-1"></i>
                                <strong>{{ $categoryName }}</strong>
                                <span class="ms-2 badge bg-light text-dark">{{ $categoryData['total_count'] ?? 0 }} items</span>
                            </td>
                        </tr>
                        @foreach ($categoryData['invoices'] as $invoiceList)
                            <tr>
                                <td></td>
                                <td>{{ $invoiceList->product?->name ?? '—' }}</td>
                                <td>
                                    <code>{{ $invoiceList->invoice?->invoice_number ?? '—' }}</code>
                                    <span class="small text-muted">({{ $invoiceList->invoice?->patient_no ?? '—' }})</span>
                                </td>
                                <td>{{ $invoiceList->invoice?->reeferDr?->name ?? '—' }}</td>
                                <td class="text-end fw-semibold">৳ {{ $fmt($invoiceList->price - $invoiceList->discount_price) }}</td>
                                <td>{{ \Carbon\Carbon::parse($invoiceList->created_at)->format('d M Y') }}</td>
                                <td class="text-end text-muted">৳ {{ $fmt($invoiceList->price) }}</td>
                            </tr>
                        @endforeach
                        <tr class="report-group-row">
                            <td colspan="4" class="text-end"><strong>Category subtotal</strong></td>
                            <td class="text-end fw-bold">৳ {{ $fmt($categoryData['total_price'] - $categoryData['discount_price']) }}</td>
                            <td colspan="2"></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-5">No sales found for this filter.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>$(function(){ $('.report-select2').select2({ width:'100%', allowClear:true }); });</script>
@endpush
