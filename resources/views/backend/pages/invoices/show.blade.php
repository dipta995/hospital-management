@extends('backend.layouts.master')
@section('title')
    Invoice #{{ $singleData->invoice_number ?? '' }}
@endsection

@push('styles')
    @include('backend.layouts.partials.invoice-styles')
@endpush

@section('admin-content')
    <div class="inv-page container-fluid py-3">

        <div class="inv-hero">
            <div class="inv-hero-inner">
                <div class="inv-hero-left">
                    <div class="inv-hero-icon"><i class="fas fa-file-invoice"></i></div>
                    <div>
                        <h1 class="inv-hero-title">{{ $singleData->patient_name }}</h1>
                        <p class="inv-hero-sub">
                            Invoice <strong>{{ $singleData->invoice_number }}</strong>
                            &nbsp;·&nbsp; Phone: {{ $singleData->patient_phone ?: '—' }}
                        </p>
                    </div>
                </div>
                <div class="inv-hero-actions">
                    <a href="{{ route($pageHeader['index_route']) }}" class="inv-btn-glass">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    @if($singleData->refer_fee_total - ($singleData->costs->sum('amount')) > 0)
                        <button type="button" class="inv-btn-glass" data-bs-toggle="modal" data-bs-target="#referPaymentModal">
                            <i class="fas fa-hand-holding-usd"></i> Refer Payment
                        </button>
                    @endif
                    <a target="_blank" href="{{ route('admin.invoices.pdf-preview', $singleData->id) }}" class="inv-btn-white">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                </div>
            </div>
        </div>

        @include('backend.layouts.partials.message')

        <div class="inv-toggle-card">
            <div>
                <strong><i class="fas fa-truck me-2 text-info"></i>Delivery Complete</strong>
                <div class="text-muted small mt-1">Mark when all reports have been delivered to patient</div>
            </div>
            <div class="form-check form-switch mb-0" style="transform:scale(1.3);">
                <input onclick="activeData({{ $singleData->id }},'/admin/invoices')"
                       {{ $singleData->status == \App\Models\Invoice::$deliveryStatusArray[1] ? 'checked' : '' }}
                       class="form-check-input" type="checkbox"
                       id="status_switch{{ $singleData->id }}">
            </div>
        </div>

        <div class="inv-kpi-grid" style="grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));">
            <div class="inv-kpi">
                <div class="inv-kpi-icon collection"><i class="fas fa-receipt"></i></div>
                <div>
                    <div class="inv-kpi-label">Total</div>
                    <div class="inv-kpi-value">৳{{ number_format($singleData->total_amount, 2) }}</div>
                </div>
            </div>
            <div class="inv-kpi">
                <div class="inv-kpi-icon discount"><i class="fas fa-tags"></i></div>
                <div>
                    <div class="inv-kpi-label">Discount</div>
                    <div class="inv-kpi-value">৳{{ number_format($singleData->discount_amount, 2) }}</div>
                </div>
            </div>
            <div class="inv-kpi">
                <div class="inv-kpi-icon own"><i class="fas fa-user-md"></i></div>
                <div>
                    <div class="inv-kpi-label">Doctor</div>
                    <div class="inv-kpi-value" style="font-size:0.95rem;">{{ $singleData->reeferDr->name ?? '—' }}</div>
                </div>
            </div>
            <div class="inv-kpi">
                <div class="inv-kpi-icon other"><i class="fas fa-share-alt"></i></div>
                <div>
                    <div class="inv-kpi-label">Referred By</div>
                    <div class="inv-kpi-value" style="font-size:0.95rem;">{{ $singleData->reeferBy->name ?? '—' }}</div>
                </div>
            </div>
        </div>

        @if(!empty($singleData->note))
            <div class="inv-toggle-card" style="background:linear-gradient(135deg,#fffbeb,#fef3c7);border-color:#fde68a;">
                <div><strong><i class="fas fa-sticky-note me-2 text-warning"></i>Note</strong>
                    <div class="mt-1">{{ $singleData->note }}</div>
                </div>
            </div>
        @endif

        <div class="inv-section">
            <div class="inv-section-head"><i class="fas fa-flask"></i> Test Items & Lab Status</div>
            <div class="inv-section-body p-0">
                <div class="inv-table-wrap" style="border:none;border-radius:0;">
                    <div class="table-responsive">
                        <table class="table inv-table mb-0">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Test Name</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th class="text-end">Downloads</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($singleData->invoiceList as $i => $item)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td><strong>{{ $item->product->name }}</strong></td>
                                    <td><span class="inv-amount">৳{{ number_format($item->price, 2) }}</span></td>
                                    <td>
                                        <span class="inv-status
                                            @if($item->status == \App\Models\InvoiceList::$statusArray[0]) pending
                                            @elseif($item->status == \App\Models\InvoiceList::$statusArray[1]) pending
                                            @elseif($item->status == \App\Models\InvoiceList::$statusArray[2]) complete
                                            @else delivery @endif">
                                            <span class="inv-status-dot"></span>
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="inv-actions justify-content-end">
                                            @if($item->status == \App\Models\InvoiceList::$statusArray[2])
                                                @if($item->document != null)
                                                    <a class="inv-act pdf" target="_blank"
                                                       href="{{ route('admin.lab.report.file-download', $item->id) }}" title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                @endif
                                                @if($item->test_report != null)
                                                    <a class="inv-act view" target="_blank"
                                                       href="{{ route('admin.lab.report.pdf-preview', $item->id) }}" title="Report">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                @endif
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @if($reports->isNotEmpty())
            <div class="inv-section">
                <div class="inv-section-head"><i class="fas fa-file-export"></i> Test Report Exports</div>
                <div class="inv-section-body p-0">
                    <div class="inv-table-wrap" style="border:none;border-radius:0;">
                        <div class="table-responsive">
                            <table class="table inv-table mb-0">
                                <thead>
                                <tr><th>Test Name</th><th class="text-end">Export</th></tr>
                                </thead>
                                <tbody>
                                @foreach($reports as $item)
                                    <tr>
                                        <td>{{ optional($item->invoiceItem->product)->name }}</td>
                                        <td class="text-end">
                                            <a class="inv-act pdf" target="_blank"
                                               href="{{ route('admin.preview-pdf-report', $item->id) }}">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if($singleData->refer_fee_total - ($singleData->costs->sum('amount')) > 0)
        <div class="modal fade inv-modal" id="referPaymentModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-hand-holding-usd me-2"></i>Refer Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="post" action="{{ route('admin.costs.store') }}">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="pc_payment" value="pc_payment">
                            <input type="hidden" name="cost_category_id" value="{{ \App\Models\Setting::get('diagnostic_refer_cost_category') }}">
                            <input type="hidden" name="refer_id" value="{{ $singleData->refer_id }}">
                            <input type="hidden" name="invoice_id" value="{{ $singleData->id }}">
                            <input type="hidden" name="reason" value=" Refer Payment">

                            <div class="mb-3">
                                <label class="form-label">Refer Name</label>
                                <input type="text" readonly class="form-control" name="reefer_name"
                                       value="{{ $singleData->reeferBy->name ?? 'N/A' }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Amount</label>
                                <input type="number" class="form-control" name="amount"
                                       value="{{ $singleData->refer_fee_total - ($singleData->costs->sum('amount')) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Account No</label>
                                <input type="text" class="form-control" name="account_no" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Payment Method</label>
                                <select class="form-select" name="payment_type" required>
                                    <option value="" disabled selected>Select method</option>
                                    @foreach(\App\Models\Invoice::$paymentArray as $payItem)
                                        <option value="{{ $payItem }}">{{ $payItem }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="inv-btn-filter">Confirm Payment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
