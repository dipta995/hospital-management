@extends('backend.layouts.master')
@section('title')
    Refer Payment — {{ $pageHeader['title'] }}
@endsection
@push('styles')
    @include('backend.layouts.partials.report-styles')
@endpush
@section('admin-content')
    @php $fmt = fn ($n) => number_format((float) $n, 2); @endphp

    <div class="inv-page container-fluid py-3">
        @include('backend.layouts.partials.report-hero', [
            'reportTitle' => 'Pay Referrer Dues',
            'reportSubtitle' => 'Select invoices and pay outstanding refer fees in bulk',
            'reportIcon' => 'fa-money-check-alt',
            'resetRoute' => route('admin.reports.references.payment'),
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
                <div class="inv-kpi-icon"><i class="fas fa-check"></i></div>
                <div>
                    <div class="inv-kpi-label">Already Paid</div>
                    <div class="inv-kpi-value text-success">৳ {{ $fmt($totalPaidAmount ?? 0) }}</div>
                </div>
            </div>
            <div class="inv-kpi">
                <div class="inv-kpi-icon discount"><i class="fas fa-exclamation-circle"></i></div>
                <div>
                    <div class="inv-kpi-label">Outstanding</div>
                    <div class="inv-kpi-value text-danger">৳ {{ $fmt($totalDueAmount ?? 0) }}</div>
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
                    <label class="form-label">Refer by <span class="text-danger">*</span></label>
                    <select class="form-select report-select2" name="refer_id" id="refer_id" required>
                        <option value="">Choose referrer</option>
                        @foreach($reffers as $item)
                            <option @selected(request('refer_id') == $item->id) value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="inv-filter-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Load invoices</button>
                    <a href="{{ route('admin.reports.references') }}" class="btn btn-outline-secondary btn-sm">View report</a>
                </div>
            </form>

            @if(request('refer_id'))
                <div class="report-pay-panel mx-3 mt-3">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Selected total</label>
                            <div class="fs-4 fw-bold text-primary">৳ <span id="total_amount_by_checkin">0.00</span></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Payment method</label>
                            <select class="form-select" id="payment_type" required>
                                <option value="" disabled selected>Select method</option>
                                @foreach(\App\Models\Invoice::$paymentArray as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="hidden" value="{{ request('refer_id') }}" id="refer_id_hidden">
                            <button type="button" class="btn btn-success btn-lg w-100" id="payNow">
                                <i class="fas fa-paper-plane"></i> Pay selected
                            </button>
                        </div>
                    </div>
                    @if(request('refer_id'))
                        <p class="small text-muted mb-0 mt-2"><i class="fas fa-info-circle"></i> Only invoices fully paid by the patient can be selected for refer payment.</p>
                    @endif
                </div>
            @else
                <div class="p-4 text-center text-muted">
                    <i class="fas fa-user-tag fa-2x mb-2 opacity-50"></i>
                    <p class="mb-0">Select a <strong>Refer by</strong> person above to load payable invoices.</p>
                </div>
            @endif

            @if(request('refer_id'))
            <div class="table-responsive">
                <table class="table inv-table mb-0">
                    <thead>
                    <tr>
                        <th style="width:40px"></th>
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
                    @foreach($datas as $item)
                        @php
                            $paid = $item->costs->sum('amount');
                            $unpaid = $item->refer_fee_total - $paid;
                            $invDue = $item->total_amount - ($item->paid_amount_sum_paid_amount ?? 0);
                            $canPay = $unpaid > 0 && $invDue <= 0;
                        @endphp
                        <tr>
                            <td>
                                @if($canPay)
                                    <input type="checkbox" class="invoice-check form-check-input" value="{{ $item->id }}">
                                    <input type="hidden" class="refer_id" value="{{ $item->refer_id }}">
                                    <input type="hidden" class="invoice_id" value="{{ $item->id }}">
                                    <input type="hidden" class="amount" value="{{ $unpaid }}">
                                @elseif($unpaid > 0)
                                    <span class="text-muted" title="Patient invoice not fully paid">—</span>
                                @endif
                            </td>
                            <td @if($invDue > 0) class="text-danger" @endif>
                                <strong>{{ $item->invoice_number }}</strong>
                                <div class="small">{{ $item->creation_date }}</div>
                                @if($invDue > 0)<small class="text-danger">Patient due ৳{{ $fmt($invDue) }}</small>@endif
                            </td>
                            <td>{{ $item->reeferBy->name ?? '—' }}</td>
                            <td>{{ $item->reeferDr->name ?? '—' }}</td>
                            <td>{{ $item->patient_name }}</td>
                            <td class="text-end">৳ {{ $fmt($item->refer_fee_total) }}</td>
                            <td class="text-end">৳ {{ $fmt($paid) }}</td>
                            <td class="text-end">৳ {{ $fmt($unpaid) }}</td>
                            <td>
                                @if($unpaid > 0)
                                    <span class="rep-badge rep-badge-pending">Pending</span>
                                @else
                                    <span class="rep-badge rep-badge-paid">Paid</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(function () {
        $('.report-select2').select2({ width: '100%', placeholder: 'Choose referrer', allowClear: true });
    });
    document.addEventListener('DOMContentLoaded', () => {
        const checkboxes = document.querySelectorAll('.invoice-check');
        const payNowBtn = document.getElementById('payNow');
        if (!payNowBtn) return;
        const paymentTypeSelect = document.getElementById('payment_type');
        const referId = document.getElementById('refer_id_hidden');
        const totalDisplay = document.getElementById('total_amount_by_checkin');

        function updateTotalAmount() {
            let total = 0;
            document.querySelectorAll('.invoice-check:checked').forEach(checkedBox => {
                const row = checkedBox.closest('tr');
                total += parseFloat(row.querySelector('.amount').value) || 0;
            });
            totalDisplay.textContent = total.toFixed(2);
        }

        checkboxes.forEach(cb => cb.addEventListener('change', updateTotalAmount));

        payNowBtn.addEventListener('click', async () => {
            const selectedData = [];
            document.querySelectorAll('.invoice-check:checked').forEach(checkedBox => {
                const row = checkedBox.closest('tr');
                selectedData.push({
                    invoice_id: row.querySelector('.invoice_id').value,
                    refer_id: row.querySelector('.refer_id').value,
                    amount: row.querySelector('.amount').value
                });
            });

            if (!referId?.value) { alert('Please select a Refer by.'); return; }
            if (!paymentTypeSelect.value) { alert('Please select a payment method.'); return; }
            if (!selectedData.length) { alert('Please select at least one invoice.'); return; }

            payNowBtn.disabled = true;
            const response = await fetch("{{ route('admin.cost.store-multiple') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({
                    payment_type: paymentTypeSelect.value,
                    refer_id: referId.value,
                    invoices: selectedData
                })
            });

            if (response.ok) {
                alert('Payment submitted successfully!');
                location.reload();
            } else {
                alert('There was a problem submitting the payment.');
                payNowBtn.disabled = false;
            }
        });
    });
</script>
@endpush
