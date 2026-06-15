@extends('backend.layouts.master')
@section('title')
    {{ $pageHeader['title'] }}
    @php
        $userGuard = Auth::guard('admin')->user();
    @endphp
@endsection

@push('styles')
    @include('backend.layouts.partials.invoice-styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
@endpush

@section('admin-content')
    <div class="inv-page container-fluid py-3">

        {{-- Hero --}}
        <div class="inv-hero">
            <div class="inv-hero-inner">
                <div class="inv-hero-left">
                    <div class="inv-hero-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                    <div>
                        <h1 class="inv-hero-title">Invoice Management</h1>
                        <p class="inv-hero-sub">Track collections, filter invoices & manage payments</p>
                    </div>
                </div>
                <div class="inv-hero-actions">
                    <a href="{{ route('admin.invoices.index') }}" class="inv-btn-glass">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </a>
                    @if($userGuard->can('invoices.create'))
                        <a href="{{ route($pageHeader['create_route']) }}" class="inv-btn-white">
                            <i class="fas fa-plus"></i> New Invoice
                        </a>
                    @endif
                </div>
            </div>
        </div>

        @include('backend.layouts.partials.message')

        {{-- KPI Cards --}}
        <div class="inv-kpi-grid">
            <div class="inv-kpi">
                <div class="inv-kpi-icon own"><i class="fas fa-user-check"></i></div>
                <div>
                    <div class="inv-kpi-label">My Collection</div>
                    <div class="inv-kpi-value">৳ {{ number_format($my_collection, 2) }}</div>
                </div>
            </div>
            @if ($userGuard->can('reports.amounts'))
                <div class="inv-kpi">
                    <div class="inv-kpi-icon other"><i class="fas fa-users"></i></div>
                    <div>
                        <div class="inv-kpi-label">Others</div>
                        <div class="inv-kpi-value">৳ {{ number_format($other_collection, 2) }}</div>
                    </div>
                </div>
                <div class="inv-kpi">
                    <div class="inv-kpi-icon collection"><i class="fas fa-hand-holding-usd"></i></div>
                    <div>
                        <div class="inv-kpi-label">Total Collection</div>
                        <div class="inv-kpi-value">৳ {{ number_format($total_paid_amount, 2) }}</div>
                    </div>
                </div>
                <div class="inv-kpi">
                    <div class="inv-kpi-icon discount"><i class="fas fa-tags"></i></div>
                    <div>
                        <div class="inv-kpi-label">Discount</div>
                        <div class="inv-kpi-value">৳ {{ number_format($discount_amount, 2) }}</div>
                    </div>
                </div>
                <div class="inv-kpi">
                    <div class="inv-kpi-icon due"><i class="fas fa-exclamation-circle"></i></div>
                    <div>
                        <div class="inv-kpi-label">Total Due</div>
                        <div class="inv-kpi-value">৳ {{ number_format($total_due_amount, 2) }}</div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Filters --}}
        <div class="inv-panel">
            <div class="inv-panel-head" data-bs-toggle="collapse" data-bs-target="#invFilterCollapse">
                <h6><i class="fas fa-sliders-h"></i> Advanced Filters</h6>
                <i class="fas fa-chevron-down text-muted"></i>
            </div>
            <div class="collapse show" id="invFilterCollapse">
                <div class="inv-panel-body">
                    <form action="{{ route('admin.invoices.index') }}" method="GET">
                        <div class="row g-3 align-items-end">
                            @if (!$userGuard->can('invoices.desk'))
                                <div class="col-md-2">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control"
                                           value="{{ request('start_date') }}">
                                </div>
                                <div class="col-md-2">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control"
                                           value="{{ request('end_date') }}">
                                </div>
                            @endif
                            <div class="col-md-3">
                                <label for="select2" class="form-label">Doctor</label>
                                <select class="form-select" name="dr_refer_id" id="select2">
                                    <option value="">All Doctors</option>
                                    @foreach($reffers as $item)
                                        <option @selected(old('dr_refer_id', request('dr_refer_id')) == $item->id) value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="invoice_by" class="form-label">Invoice By</label>
                                <select name="admin_id" id="invoice_by" class="form-select">
                                    <option value="">All Staff</option>
                                    @foreach(\App\Models\Admin::where('branch_id', auth()->user()->branch_id)->get() as $item)
                                        <option value="{{ $item->id }}" @selected(old('admin_id', request('admin_id')) == $item->id)>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="due_filter" class="form-label">Due Status</label>
                                <select name="due" id="due_filter" class="form-select">
                                    <option value="">All</option>
                                    <option value="yes" {{ request('due') == 'yes' ? 'selected' : '' }}>Has Due</option>
                                    <option value="no" {{ request('due') == 'no' ? 'selected' : '' }}>Fully Paid</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="fieldSelector" class="form-label">Search By</label>
                                <select id="fieldSelector" class="form-select">
                                    <option value="">—</option>
                                    <option value="invoice">Invoice No</option>
                                    <option value="patient">Patient ID</option>
                                </select>
                            </div>
                            <div class="col-md-2" id="invoiceField" style="display:none;">
                                <label for="invoice_number" class="form-label">Invoice No</label>
                                <input type="text" name="invoice_number" id="invoice_number" class="form-control"
                                       value="{{ request('invoice_number') }}" placeholder="INV-...">
                            </div>
                            <div class="col-md-2" id="patientField" style="display:none;">
                                <label for="user_id" class="form-label">Patient ID</label>
                                <input type="text" name="user_id" id="user_id" class="form-control"
                                       value="{{ request('user_id') }}">
                            </div>
                        </div>
                        <div class="inv-filter-actions">
                            <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                            <button type="submit" class="inv-btn-filter"><i class="fas fa-search me-1"></i> Apply Filters</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="inv-table-wrap inv-list-table-wrap inv-list-table-wide" data-inv-hscroll>
            <div class="inv-hscroll-top" aria-hidden="true">
                <div class="inv-hscroll-top-inner"></div>
            </div>
            <div class="table-responsive inv-hscroll-body">
                <table class="table inv-table">
                    <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Referred</th>
                        <th>By</th>
                        <th>Total</th>
                        <th>Paid</th>
                        <th>Due</th>
                        <th>Status</th>
                        <th class="inv-actions-cell">
                            <div class="inv-actions-head">
                                <span title="Edit"><i class="fas fa-pen"></i></span>
                                <span title="Pay Due / Return"><i class="fas fa-dollar-sign"></i></span>
                                <span title="PDF"><i class="fas fa-file-pdf"></i></span>
                                <span title="View"><i class="fas fa-eye"></i></span>
                                <span title="Delete"><i class="fas fa-trash"></i></span>
                            </div>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($datas as $item)
                        @php
                            $dueAmount = $item->total_amount - $item->paid_amount_sum_paid_amount;
                            $paidPct = $item->total_amount > 0
                                ? min(100, round(($item->paid_amount_sum_paid_amount / $item->total_amount) * 100))
                                : 100;
                            $canEditRow = $userGuard->can('invoices.edit')
                                && (\Carbon\Carbon::parse($item->creation_date)->setTimezone('Asia/Dhaka')->isToday()
                                    || auth()->user()->hasRole('Owner'));
                        @endphp
                        <tr id="table-data{{ $item->id }}">
                            <td data-label="Invoice">
                                <span class="inv-inv-no">{{ $item->invoice_number }}</span>
                            </td>
                            <td data-label="Patient">
                                <div class="inv-patient-name">{{ $item->patient_name ?? 'NA' }}</div>
                                <div class="inv-patient-id">ID: {{ $item->patient_no }}</div>
                            </td>
                            <td data-label="Doctor">{{ $item->reeferDr->name ?? '—' }}</td>
                            <td data-label="Referred">
                                {{ $item->reeferBy->name ?? '—' }}
                                <div class="inv-patient-id">Ref: ৳{{ $item->refer_fee_total - ($item->costs->sum('amount')) }}</div>
                            </td>
                            <td data-label="By">{{ $item->admin->name ?? '—' }}</td>
                            <td data-label="Total">
                                <span class="inv-amount">৳{{ number_format($item->total_amount, 2) }}</span>
                                @if($item->discount_amount > 0)
                                    <div class="inv-patient-id">Disc: ৳{{ number_format($item->discount_amount, 2) }}</div>
                                @endif
                            </td>
                            <td data-label="Paid">
                                <span class="inv-amount paid">৳{{ number_format($item->paid_amount_sum_paid_amount ?? 0, 2) }}</span>
                                <div class="inv-progress-wrap">
                                    <div class="inv-progress-bar">
                                        <div class="inv-progress-fill" style="width: {{ $paidPct }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Due">
                                @if($dueAmount < -0.009)
                                    <span class="inv-amount overpaid">৳{{ number_format($dueAmount, 2) }}</span>
                                    <div class="inv-patient-id">Overpaid ৳{{ number_format(abs($dueAmount), 2) }}</div>
                                @else
                                    <span class="inv-amount {{ $dueAmount > 0 ? 'due' : 'paid' }}">
                                        ৳{{ number_format($dueAmount, 2) }}
                                    </span>
                                @endif
                            </td>
                            <td data-label="Status">
                                <div class="d-flex flex-column gap-1">
                                    <span class="inv-status {{ $item->isFullyProcessed() ? 'complete' : 'pending' }}">
                                        <span class="inv-status-dot"></span>
                                        {{ $item->isFullyProcessed() ? 'Lab Complete' : 'Lab Pending' }}
                                    </span>
                                    <span class="inv-status delivery">
                                        <span class="inv-status-dot"></span>
                                        {{ $item->status }}
                                    </span>
                                </div>
                            </td>
                            <td data-label="Actions" class="inv-actions-cell">
                                <div class="inv-actions-grid">
                                    @if ($canEditRow)
                                        <a href="{{ route($pageHeader['edit_route'], $item->id) }}"
                                           class="inv-act edit" title="Edit"><i class="fas fa-pen"></i></a>
                                    @else
                                        <span class="inv-act-slot" aria-hidden="true"></span>
                                    @endif

                                    @if($dueAmount > 0.009)
                                        <button type="button" class="inv-act pay"
                                                data-bs-toggle="modal"
                                                data-bs-target="#payDueModal{{ $item->id }}" title="Pay Due">
                                            <i class="fas fa-dollar-sign"></i>
                                        </button>
                                    @elseif($dueAmount < -0.009)
                                        <button type="button" class="inv-act return"
                                                data-bs-toggle="modal"
                                                data-bs-target="#returnOverpayModal{{ $item->id }}" title="Return Overpay">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    @else
                                        <span class="inv-act-slot" aria-hidden="true"></span>
                                    @endif

                                    <a target="_blank" href="{{ route('admin.invoices.pdf-preview', $item->id) }}"
                                       class="inv-act pdf" title="PDF"><i class="fas fa-file-pdf"></i></a>

                                    <a href="{{ route('admin.invoices.show', $item->id) }}"
                                       class="inv-act view" title="View"><i class="fas fa-eye"></i></a>

                                    @if ($userGuard->can('invoices.delete'))
                                        <a href="javascript:void(0)" class="inv-act del" title="Delete"
                                           onclick="dataDelete({{ $item->id }},'{{ $pageHeader['base_url'] }}')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    @else
                                        <span class="inv-act-slot" aria-hidden="true"></span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">
                                <div class="inv-empty">
                                    <i class="fas fa-file-invoice"></i>
                                    <p>No invoices found for selected filters.</p>
                                    @if($userGuard->can('invoices.create'))
                                        <a href="{{ route($pageHeader['create_route']) }}" class="inv-btn-white" style="color:var(--inv-primary);display:inline-flex;">
                                            <i class="fas fa-plus"></i> Create First Invoice
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pay Due modals (outside table for clean alignment) --}}
        @foreach($datas as $item)
            @php
                $dueAmountModal = $item->total_amount - $item->paid_amount_sum_paid_amount;
            @endphp
            @if($dueAmountModal > 0.009)
                <div class="modal fade inv-modal" id="payDueModal{{ $item->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><i class="fas fa-dollar-sign me-2"></i>Pay Due — {{ $item->invoice_number }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                @php
                                    $customerBalance = \App\Models\CustomerBalance::where('user_id', $item->user_id)
                                        ->where('branch_id', auth()->user()->branch_id)->first();
                                    $currentBalance = $customerBalance->balance ?? 0;
                                @endphp
                                <form method="post" action="{{ route('admin.invoices.due-pay', $item->id) }}">
                                    @csrf
                                    <input type="hidden" name="is_return" value="0">
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <label class="form-label">Due Amount</label>
                                            <input type="text" class="form-control fw-bold text-danger"
                                                   value="৳ {{ number_format($dueAmountModal, 2) }}" readonly>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label">Customer Balance</label>
                                            <input type="text" class="form-control"
                                                   value="৳ {{ number_format($currentBalance, 2) }}" readonly>
                                        </div>
                                        <div class="col-12">
                                            <label for="due_pay_{{ $item->id }}" class="form-label">Pay Amount</label>
                                            <input type="number" step="0.01" min="0.01" class="form-control"
                                                   value="{{ number_format($dueAmountModal, 2, '.', '') }}"
                                                   id="due_pay_{{ $item->id }}" name="due_pay" required>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="1"
                                                       id="pay_from_balance_{{ $item->id }}" name="pay_from_balance">
                                                <label class="form-check-label" for="pay_from_balance_{{ $item->id }}">
                                                    Pay from customer balance
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="inv-btn-filter w-100">
                                                <i class="fas fa-check me-1"></i> Confirm Payment
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($dueAmountModal < -0.009)
                <div class="modal fade inv-modal" id="returnOverpayModal{{ $item->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><i class="fas fa-undo me-2"></i>Return Overpayment — {{ $item->invoice_number }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form method="post" action="{{ route('admin.invoices.due-pay', $item->id) }}">
                                    @csrf
                                    <input type="hidden" name="is_return" value="1">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label">Overpaid Amount</label>
                                            <input type="text" class="form-control fw-bold text-primary"
                                                   value="৳ {{ number_format(abs($dueAmountModal), 2) }}" readonly>
                                        </div>
                                        <div class="col-12">
                                            <label for="return_pay_{{ $item->id }}" class="form-label">Return Amount</label>
                                            <input type="number" step="0.01" min="0.01" class="form-control"
                                                   value="{{ number_format(abs($dueAmountModal), 2, '.', '') }}"
                                                   id="return_pay_{{ $item->id }}" name="due_pay" required>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="1"
                                                       id="add_to_balance_{{ $item->id }}" name="add_to_balance">
                                                <label class="form-check-label" for="add_to_balance_{{ $item->id }}">
                                                    Add returned amount to customer balance
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="inv-btn-filter w-100">
                                                <i class="fas fa-undo me-1"></i> Confirm Return
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

        <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
            <span class="text-muted small">Showing {{ $datas->count() }} of {{ $datas->total() }} invoices</span>
            {!! $datas->appends(request()->query())->links() !!}
        </div>
    </div>
@endsection

@push('scripts')
    @include('backend.layouts.partials.invoice-hscroll-sync')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#select2').select2({ placeholder: "All Doctors", allowClear: true, width: '100%' });

            let $selector = $('#fieldSelector');
            let $invoice = $('#invoiceField');
            let $patient = $('#patientField');

            function updateFields(val) {
                $invoice.toggle(val === 'invoice');
                $patient.toggle(val === 'patient');
                if (val === 'invoice') $('#user_id').val('');
                else if (val === 'patient') $('#invoice_number').val('');
                else { $('#invoice_number, #user_id').val(''); }
            }

            @if(request('invoice_number'))
                $selector.val('invoice'); updateFields('invoice');
            @elseif(request('user_id'))
                $selector.val('patient'); updateFields('patient');
            @else
                updateFields('');
            @endif

            $selector.on('change', function () { updateFields($(this).val()); });
        });
    </script>
@endpush
