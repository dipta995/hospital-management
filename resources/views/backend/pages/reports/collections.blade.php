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
        $periodLabel = request('start_date') || request('end_date')
            ? (request('start_date') ?: '…') . ' → ' . (request('end_date') ?: '…')
            : 'Today (' . now()->format('d M Y') . ')';
        $gross = ($overall_total_amount ?? 0) + ($overall_total_discount ?? 0);
    @endphp

    <div class="inv-page container-fluid py-3">
        @include('backend.layouts.partials.report-hero', [
            'reportTitle' => 'Diagnostic Collections',
            'reportSubtitle' => 'Payments received against lab/diagnostic invoices',
            'reportIcon' => 'fa-hand-holding-usd',
            'resetRoute' => route('admin.reports.collections'),
        ])

        @include('backend.layouts.partials.message')

        <div class="inv-kpi-grid">
            <div class="inv-kpi">
                <div class="inv-kpi-icon collection"><i class="fas fa-coins"></i></div>
                <div>
                    <div class="inv-kpi-label">Total Collected</div>
                    <div class="inv-kpi-value">৳ {{ $fmt($overall_total_collection ?? 0) }}</div>
                </div>
            </div>
            <div class="inv-kpi">
                <div class="inv-kpi-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                <div>
                    <div class="inv-kpi-label">Invoice Amount (net)</div>
                    <div class="inv-kpi-value">৳ {{ $fmt($overall_total_amount ?? 0) }}</div>
                </div>
            </div>
            <div class="inv-kpi">
                <div class="inv-kpi-icon discount"><i class="fas fa-percent"></i></div>
                <div>
                    <div class="inv-kpi-label">Total Discount</div>
                    <div class="inv-kpi-value">৳ {{ $fmt($overall_total_discount ?? 0) }}</div>
                </div>
            </div>
            <div class="inv-kpi">
                <div class="inv-kpi-icon"><i class="fas fa-calculator"></i></div>
                <div>
                    <div class="inv-kpi-label">Gross (before discount)</div>
                    <div class="inv-kpi-value">৳ {{ $fmt($gross) }}</div>
                </div>
            </div>
        </div>

        <div class="inv-panel mb-3">
            <form action="" method="GET" class="inv-filter-toolbar">
                <div class="filter-field">
                    <label class="form-label">From date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="filter-field">
                    <label class="form-label">To date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="filter-field" style="max-width:140px">
                    <label class="form-label">Export PDF</label>
                    <select class="form-select" name="export">
                        <option value="">No</option>
                        <option value="pdf" @selected(request('export') === 'pdf')>Download PDF</option>
                    </select>
                </div>
                <div class="inv-filter-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Apply</button>
                </div>
            </form>

            <div class="p-3 pb-0">
                <span class="report-period-pill"><i class="fas fa-calendar-alt"></i> {{ $periodLabel }}</span>
            </div>

            <div class="table-responsive">
                <table class="table inv-table mb-0">
                    <tbody>
                    @forelse($datas as $date => $invoices)
                        <tr class="report-date-row">
                            <td colspan="3"><i class="fas fa-calendar-day me-1"></i> {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</td>
                            <td><strong>Day total</strong></td>
                            <td>Subtotal ৳{{ $fmt($invoices->sum('total_amount') + $invoices->sum('total_discount')) }}</td>
                            <td>Disc ৳{{ $fmt($invoices->sum('total_discount')) }}</td>
                            <td colspan="2"><strong>Collected ৳{{ $fmt($invoices->sum('total_collection')) }}</strong></td>
                        </tr>
                        <tr class="report-section-head">
                            <th>#</th>
                            <th>Invoice</th>
                            <th>Tests / Products</th>
                            <th>Subtotal</th>
                            <th>Discount</th>
                            <th>Payment</th>
                            <th>Doctor</th>
                            <th>Refer By</th>
                        </tr>
                        @foreach($invoices as $invoice_id => $group)
                            @php
                                $invoice = isset($group['data']) ? collect($group['data'])->first()->invoice ?? null : null;
                            @endphp
                            <tr class="report-group-row">
                                <td colspan="8">
                                    <i class="fas fa-file-invoice me-1"></i>
                                    Invoice <strong>{{ $invoice?->invoice_number ?? 'N/A' }}</strong>
                                </td>
                            </tr>
                            @if(isset($group['data']))
                                @foreach($group['data'] as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><code>{{ $invoice?->invoice_number ?? 'N/A' }}</code></td>
                                        <td class="small">
                                            @foreach($invoice?->invoiceList ?? [] as $pr)
                                                <span class="d-block">{{ $pr->product?->name ?? '—' }}</span>
                                            @endforeach
                                        </td>
                                        <td>৳ {{ $fmt(($invoice?->total_amount ?? 0) + ($invoice?->discount_amount ?? 0)) }}</td>
                                        <td>৳ {{ $fmt($invoice?->discount_amount ?? 0) }}</td>
                                        <td class="fw-semibold text-success">৳ {{ $fmt($item->paid_amount) }}</td>
                                        <td>{{ $invoice?->reeferDr?->name ?? '—' }}</td>
                                        <td>{{ $invoice?->reeferBy?->name ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-5">No collection records for this period.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
