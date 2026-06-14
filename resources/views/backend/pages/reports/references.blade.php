@extends('backend.layouts.master')
@section('title')
    {{ $pageHeader['title'] }}
@endsection
@push('styles')
    @include('backend.layouts.partials.report-styles')
@endpush
@section('admin-content')
    @php $fmt = fn ($n) => number_format((float) $n, 2); @endphp

    <div class="inv-page container-fluid py-3">
        @include('backend.layouts.partials.report-hero', [
            'reportTitle' => 'Referrer Commission Report',
            'reportSubtitle' => 'Refer fees earned, paid and outstanding by invoice',
            'reportIcon' => 'fa-user-friends',
            'resetRoute' => route('admin.reports.references'),
        ])

        @include('backend.layouts.partials.message')

        <div class="inv-kpi-grid">
            <div class="inv-kpi">
                <div class="inv-kpi-icon collection"><i class="fas fa-hand-holding-usd"></i></div>
                <div>
                    <div class="inv-kpi-label">Total Refer Fee</div>
                    <div class="inv-kpi-value">৳ {{ $fmt($totalAmount ?? 0) }}</div>
                </div>
            </div>
            <div class="inv-kpi">
                <div class="inv-kpi-icon"><i class="fas fa-check-circle"></i></div>
                <div>
                    <div class="inv-kpi-label">Paid to Referrer</div>
                    <div class="inv-kpi-value text-success">৳ {{ $fmt($totalPaidAmount ?? 0) }}</div>
                </div>
            </div>
            <div class="inv-kpi">
                <div class="inv-kpi-icon discount"><i class="fas fa-clock"></i></div>
                <div>
                    <div class="inv-kpi-label">Outstanding Due</div>
                    <div class="inv-kpi-value text-danger">৳ {{ $fmt($totalDueAmount ?? 0) }}</div>
                </div>
            </div>
            <div class="inv-kpi">
                <div class="inv-kpi-icon"><i class="fas fa-calendar"></i></div>
                <div>
                    <div class="inv-kpi-label">Period</div>
                    <div class="inv-kpi-value" style="font-size:0.95rem">{{ $startDate }} → {{ $endDate }}</div>
                </div>
            </div>
        </div>

        <div class="inv-panel mb-3">
            <form action="" method="GET" class="inv-filter-toolbar">
                <div class="filter-field">
                    <label class="form-label">From</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="filter-field">
                    <label class="form-label">To</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="filter-field">
                    <label class="form-label">Refer by</label>
                    <select class="form-select report-select2" name="refer_id" id="refer_id">
                        <option value="">All referrers</option>
                        @foreach($reffers as $item)
                            <option @selected(request('refer_id') == $item->id) value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-field" style="max-width:130px">
                    <label class="form-label">PDF</label>
                    <select class="form-select" name="export">
                        <option value="">No</option>
                        <option value="pdf" @selected(request('export') === 'pdf')>Export</option>
                    </select>
                </div>
                <div class="inv-filter-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Apply</button>
                    @can('reports.index')
                        <a href="{{ route('admin.reports.references.payment') }}" class="btn btn-outline-primary btn-sm">Pay dues →</a>
                    @endcan
                </div>
            </form>

            <div class="table-responsive">
                <table class="table inv-table mb-0">
                    <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Refer By</th>
                        <th>Doctor</th>
                        <th>Patient</th>
                        <th class="text-end">Refer Fee</th>
                        <th class="text-end">Paid</th>
                        <th class="text-end">Unpaid</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($datas as $item)
                        @php
                            $paid = $item->costs->sum('amount');
                            $unpaid = $item->refer_fee_total - $paid;
                            $invDue = $item->total_amount - ($item->paid_amount_sum_paid_amount ?? 0);
                        @endphp
                        <tr @if($invDue > 0) class="table-warning" @endif>
                            <td>
                                <a href="{{ route('admin.invoices.show', $item->id) }}" class="fw-semibold text-decoration-none">
                                    {{ $item->invoice_number }}
                                </a>
                                <div class="small text-muted">#{{ $item->id }} · {{ $item->creation_date }}</div>
                                @if($invDue > 0)
                                    <span class="rep-badge rep-badge-due mt-1">Patient due ৳{{ $fmt($invDue) }}</span>
                                @endif
                            </td>
                            <td>{{ $item->reeferBy->name ?? '—' }}</td>
                            <td>{{ $item->reeferDr->name ?? '—' }}</td>
                            <td>
                                <strong>{{ $item->patient_name }}</strong>
                                <div class="small text-muted">Bill ৳{{ $fmt($item->total_amount + $item->discount_amount) }} · Disc ৳{{ $fmt($item->discount_amount) }}</div>
                            </td>
                            <td class="text-end fw-semibold">৳ {{ $fmt($item->refer_fee_total) }}</td>
                            <td class="text-end text-success">৳ {{ $fmt($paid) }}</td>
                            <td class="text-end">
                                ৳ {{ $fmt($unpaid) }}
                                @if($unpaid < 0)<span class="rep-badge rep-badge-extra">Extra</span>@endif
                            </td>
                            <td>
                                @if($unpaid > 0)
                                    <span class="rep-badge rep-badge-pending">Pending</span>
                                @else
                                    <span class="rep-badge rep-badge-paid">Paid</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-5">No refer records for this filter.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end p-3">{!! $datas->appends(request()->query())->links() !!}</div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(function () {
        $('.report-select2').select2({ width: '100%', placeholder: 'All referrers', allowClear: true });
    });
</script>
@endpush
