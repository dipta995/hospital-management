@extends('backend.layouts.master')

@section('title')
    {{ $pageHeader['title'] }}
@endsection

@push('styles')
    @include('backend.layouts.partials.report-styles')
    <style>
        .ref-stat-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 700;
            background: #f1f5f9;
            color: #334155;
        }
        .ref-status-paid { background: #ecfdf5; color: #047857; }
        .ref-status-pending { background: #fef2f2; color: #b91c1c; }
        .ref-status-extra { background: #eff6ff; color: #1d4ed8; }
    </style>
@endpush

@section('admin-content')
    @php
        $fmt = fn ($n) => number_format((float) $n, 2);
        $userGuard = Auth::guard('admin')->user();
    @endphp

    <div class="inv-page container-fluid py-3">
        @include('backend.layouts.partials.report-hero', [
            'reportTitle' => $pageHeader['title'],
            'reportSubtitle' => !empty($linkedDoctor)
                ? ($linkedDoctor->name . ' · Refer fee summary')
                : 'No doctor profile linked to your account',
            'reportIcon' => 'fa-user-md',
            'resetRoute' => route('admin.reports.references.doctor'),
        ])

        @include('backend.layouts.partials.message')

        @if(empty($linkedDoctor))
            <div class="inv-panel">
                <div class="p-4 text-center text-muted">
                    <i class="fas fa-exclamation-circle fa-2x mb-2"></i>
                    <p class="mb-0">Your admin account is not linked to a doctor (reefer) profile. Contact administrator.</p>
                </div>
            </div>
        @else
            <div class="inv-kpi-grid">
                <div class="inv-kpi">
                    <div class="inv-kpi-icon collection"><i class="fas fa-file-invoice"></i></div>
                    <div>
                        <div class="inv-kpi-label">Invoices</div>
                        <div class="inv-kpi-value">{{ $invoiceCount ?? 0 }}</div>
                    </div>
                </div>
                <div class="inv-kpi">
                    <div class="inv-kpi-icon collection"><i class="fas fa-hand-holding-usd"></i></div>
                    <div>
                        <div class="inv-kpi-label">Total Refer Fee</div>
                        <div class="inv-kpi-value">৳ {{ $fmt($totalAmount ?? 0) }}</div>
                    </div>
                </div>
                <div class="inv-kpi">
                    <div class="inv-kpi-icon discount"><i class="fas fa-check-circle"></i></div>
                    <div>
                        <div class="inv-kpi-label">Paid</div>
                        <div class="inv-kpi-value">৳ {{ $fmt($totalPaidAmount ?? 0) }}</div>
                    </div>
                </div>
                <div class="inv-kpi">
                    <div class="inv-kpi-icon due"><i class="fas fa-exclamation-circle"></i></div>
                    <div>
                        <div class="inv-kpi-label">Unpaid / Due</div>
                        <div class="inv-kpi-value">৳ {{ $fmt($totalDueAmount ?? 0) }}</div>
                    </div>
                </div>
            </div>

            <div class="inv-panel mb-3">
                <div class="report-period-pill mx-3 mt-3">
                    <i class="far fa-calendar-alt"></i>
                    {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} — {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                </div>
                <form method="GET" class="inv-filter-toolbar">
                    <div class="filter-field">
                        <label class="form-label" for="start_date">From</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="filter-field">
                        <label class="form-label" for="end_date">To</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="filter-field" style="max-width:120px">
                        <label class="form-label" for="export">PDF</label>
                        <select class="form-select" name="export" id="export">
                            <option value="">No</option>
                            <option value="pdf" @selected(request('export') === 'pdf')>Export</option>
                        </select>
                    </div>
                    <div class="inv-filter-actions">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Apply</button>
                    </div>
                </form>
            </div>

            <div class="inv-panel">
                <div class="inv-panel-head" style="cursor: default;">
                    <h6><i class="fas fa-table me-2 text-primary"></i> Refer Fee Details</h6>
                </div>
                <div class="table-responsive">
                    <table class="inv-table table table-hover mb-0">
                        <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Refer By</th>
                            <th>Doctor</th>
                            <th>Patient</th>
                            <th class="text-end">Bill (Disc.)</th>
                            <th class="text-end">My Fee</th>
                            <th class="text-end">Paid</th>
                            <th class="text-end">Due</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($datas as $item)
                            @php
                                $paid = (float) $item->costs->sum('amount');
                                $myFee = (float) $item->refer_fee_total;
                                $due = $myFee - $paid;
                                $billTotal = (float) $item->total_amount + (float) $item->discount_amount;
                            @endphp
                            <tr>
                                <td>
                                    @if($userGuard->can('invoices.show'))
                                        <a href="{{ route('admin.invoices.show', $item->id) }}" class="fw-semibold text-primary">
                                            {{ $item->invoice_number }}
                                        </a>
                                    @else
                                        <span class="fw-semibold">{{ $item->invoice_number }}</span>
                                    @endif
                                    <div class="small text-muted">#{{ $item->id }}</div>
                                    <div class="small text-muted">{{ $item->creation_date ? \Carbon\Carbon::parse($item->creation_date)->format('d M Y') : '—' }}</div>
                                </td>
                                <td>{{ $item->reeferBy->name ?? '—' }}</td>
                                <td>{{ $item->reeferDr->name ?? '—' }}</td>
                                <td>{{ $item->patient_name }}</td>
                                <td class="text-end">
                                    <div>৳ {{ $fmt($billTotal) }}</div>
                                    <small class="text-muted">Disc. ৳{{ $fmt($item->discount_amount) }}</small>
                                </td>
                                <td class="text-end fw-semibold">৳ {{ $fmt($myFee) }}</td>
                                <td class="text-end">৳ {{ $fmt($paid) }}</td>
                                <td class="text-end">
                                    @if($due < 0)
                                        <span class="ref-stat-pill ref-status-extra">+৳ {{ $fmt(abs($due)) }} extra</span>
                                    @else
                                        ৳ {{ $fmt($due) }}
                                    @endif
                                </td>
                                <td>
                                    @if($due > 0)
                                        <span class="ref-stat-pill ref-status-pending">Pending</span>
                                    @else
                                        <span class="ref-stat-pill ref-status-paid">Paid</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">No invoices found for this period.</td>
                            </tr>
                        @endforelse
                        </tbody>
                        @if($datas->count())
                            <tfoot>
                            <tr class="table-light fw-bold">
                                <td colspan="5" class="text-end">Period Total</td>
                                <td class="text-end">৳ {{ $fmt($totalAmount ?? 0) }}</td>
                                <td class="text-end">৳ {{ $fmt($totalPaidAmount ?? 0) }}</td>
                                <td class="text-end">৳ {{ $fmt($totalDueAmount ?? 0) }}</td>
                                <td></td>
                            </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
                <div class="d-flex justify-content-end p-3">
                    {!! $datas->appends(request()->query())->links() !!}
                </div>
            </div>
        @endif
    </div>
@endsection
