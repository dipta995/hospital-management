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
            'reportTitle' => 'Hospital Collections',
            'reportSubtitle' => 'Payments received when patients are discharged (admit release)',
            'reportIcon' => 'fa-hospital',
            'resetRoute' => route('admin.reports.recept-collections'),
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
                <div class="inv-kpi-icon"><i class="fas fa-bed"></i></div>
                <div>
                    <div class="inv-kpi-label">Bill Amount (net)</div>
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
                    @forelse($datas as $date => $admits)
                        <tr class="report-date-row">
                            <td colspan="3"><i class="fas fa-calendar-day me-1"></i> {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</td>
                            <td><strong>Day total</strong></td>
                            <td>Subtotal ৳{{ $fmt($admits->sum('total_amount') + $admits->sum('total_discount')) }}</td>
                            <td>Disc ৳{{ $fmt($admits->sum('total_discount')) }}</td>
                            <td colspan="2"><strong>Collected ৳{{ $fmt($admits->sum('total_collection')) }}</strong></td>
                        </tr>
                        <tr class="report-section-head">
                            <th>#</th>
                            <th>Admit ID</th>
                            <th>Services</th>
                            <th>Subtotal</th>
                            <th>Discount</th>
                            <th>Payment</th>
                            <th>Doctor</th>
                            <th>Refer By</th>
                        </tr>
                        @foreach($admits as $admitId => $group)
                            @php
                                $firstPayment = isset($group['data']) ? collect($group['data'])->first() : null;
                                $admit = $firstPayment?->admit;
                            @endphp
                            <tr class="report-group-row">
                                <td colspan="8"><i class="fas fa-procedures me-1"></i> Admit <strong>#{{ $admit->id ?? 'N/A' }}</strong></td>
                            </tr>
                            @if(isset($group['data']))
                                @foreach($group['data'] as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>#{{ $admit->id ?? 'N/A' }}</td>
                                        <td class="small">
                                            @foreach(($admit?->recepts ?? []) as $recept)
                                                @foreach($recept->receptList ?? [] as $pr)
                                                    <span class="d-block">{{ $pr->service->name ?? '—' }}</span>
                                                @endforeach
                                            @endforeach
                                        </td>
                                        <td>৳ {{ $fmt(($group['total_amount'] ?? 0) + ($group['total_discount'] ?? 0)) }}</td>
                                        <td>৳ {{ $fmt($group['total_discount'] ?? 0) }}</td>
                                        <td class="fw-semibold text-success">৳ {{ $fmt($item->paid_amount) }}</td>
                                        <td>{{ $admit?->drreefer?->name ?? '—' }}</td>
                                        <td>{{ $admit?->reefer?->name ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-5">No hospital collection records for this period.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
