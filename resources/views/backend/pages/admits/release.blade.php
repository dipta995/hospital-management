@extends('backend.layouts.master')

@section('title')
    Admit Release
@endsection

@section('admin-content')
<div class="main-panel">
    <div class="content-wrapper">
        @include('backend.layouts.partials.message')
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">Admit Release Summary</h4>
                            <span class="badge bg-secondary">Admit ID: {{ $admit->id }}</span>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="border rounded-3 bg-light p-3 h-100">
                                    <h5 class="mb-3">Patient Details</h5>
                                    <p class="mb-1"><strong>Name:</strong> {{ $admit->user->name ?? 'N/A' }}</p>
                                    <p class="mb-1"><strong>Phone:</strong> {{ $admit->user->phone ?? 'N/A' }}</p>
                                    <p class="mb-1"><strong>Age:</strong> {{ $admit->user->age ?? 'N/A' }}</p>
                                    <p class="mb-1"><strong>Gender:</strong> {{ $admit->user->gender ?? 'N/A' }}</p>
                                    <p class="mb-0"><strong>Address:</strong> {{ $admit->user->address ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded-3 bg-light p-3 h-100">
                                    <h5 class="mb-3">Admit Details</h5>
                                    <p class="mb-1"><strong>Admit Date:</strong> {{ $admit->admit_at ?? 'N/A' }}</p>
                                    <p class="mb-1"><strong>Release Date:</strong> {{ $admit->release_at ?? 'Not released' }}</p>
                                    <p class="mb-1"><strong>Bed/Cabin:</strong> {{ $admit->bed_or_cabin ?? 'N/A' }}</p>
                                    <p class="mb-1"><strong>Father/Spouse:</strong> {{ $admit->father_or_spouse ?? 'N/A' }}</p>
                                    <p class="mb-1"><strong>Received By:</strong> {{ $admit->received_by ?? 'N/A' }}</p>
                                    <p class="mb-0"><strong>Refer:</strong>
                                        {{ $admit->reefer->name ?? 'N/A' }}
                                        @if(!empty($admit->reefer?->phone))
                                            ({{ $admit->reefer->phone }})
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="mb-3">Receipt Summary (This Admit)</h5>
                                <div class="row g-3">
                                    <div class="col-md-3 col-6">
                                        <div class="border rounded-3 p-3 h-100 text-center bg-light">
                                            <div class="text-muted small">Total Amount</div>
                                            <div class="fw-bold fs-5">{{ number_format($total_amount, 2) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="border rounded-3 p-3 h-100 text-center bg-light">
                                            <div class="text-muted small">Total Discount</div>
                                            <div class="fw-bold fs-5 text-primary">{{ number_format($total_discount, 2) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="border rounded-3 p-3 h-100 text-center bg-light">
                                            <div class="text-muted small">Net Amount</div>
                                            <div class="fw-bold fs-5">{{ number_format($net_total, 2) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="border rounded-3 p-3 h-100 text-center bg-light">
                                            <div class="text-muted small">Total Paid</div>
                                            <div class="fw-bold fs-5 text-success">{{ number_format($total_paid, 2) }}</div>
                                            <div class="mt-1">
                                                @if($total_due > 0)
                                                    <span class="badge bg-danger">
                                                        Due: {{ number_format($total_due, 2) }}
                                                    </span>
                                                @elseif(!empty($extra_amount) && $extra_amount > 0)
                                                    <span class="badge bg-primary">
                                                        Extra: {{ number_format($extra_amount, 2) }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-success">
                                                        Due: 0.00
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="mb-0">Receipts List</h5>
                                    @if(!$admit->release_at)
                                        <a href="{{ route('admin.recepts.create').'?admitId='.$admit->id .'&for='.$admit->user_id }}" class="btn btn-sm btn-info text-white">
                                            <i class="fas fa-file-invoice-dollar"></i> Create Receipt
                                        </a>
                                    @endif
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Date</th>
                                                <th>Total</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($receipts as $recept)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $recept->created_date }}</td>
                                                    <td>{{ number_format($recept->total_amount, 2) }}</td>
                                                    <td>
                                                        <a target="_blank"
                                                           href="{{ route('admin.recepts.pdf-preview',$recept->id) }}"
                                                           class="badge bg-danger"><i class="fas fa-file-pdf"></i></a>
                                                        <br>
                                                        @if(!$admit->release_at)
                                                            <a href="{{ route('admin.recepts.edit', $recept->id) }}" class="badge bg-info"><i class="fas fa-pen"></i></a>
                                                            <a href="javascript:void(0)" class="badge bg-danger mt-1"
                                                               onclick="dataDelete({{ $recept->id }}, '{{ url('admin/recepts') }}')"><i class="fas fa-trash"></i></a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">No receipts found for this admit.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        @if(!$admit->release_at)
                            <div class="row mb-4">
                                <div class="col-md-7 mb-3 mb-md-0">
                                    <div class="border rounded-3 p-3 h-100 bg-white shadow-sm">
                                        <h5 class="mb-2">Release Payment (Global)</h5>
                                        <p class="text-muted mb-3 small">
                                            Total due for this admit: <strong>{{ number_format($total_due, 2) }}</strong>.
                                            @if(!empty($extra_amount) && $extra_amount > 0)
                                                <span class="ms-2">Extra amount: <strong>{{ number_format($extra_amount, 2) }}</strong></span>
                                            @endif
                                            You can give a discount and/or take payment now.
                                        </p>
                                        <form method="POST" action="{{ route('admin.admits.pay-due', $admit->id) }}" id="pay-due-form">
                                            @csrf
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label for="creation_date" class="form-label mb-1">Creation Date</label>
                                                    <input
                                                        type="date"
                                                        name="creation_date"
                                                        id="creation_date"
                                                        class="form-control form-control-sm"
                                                        value="{{ old('creation_date', now('Asia/Dhaka')->format('Y-m-d')) }}"
                                                    >
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="discount_amount" class="form-label mb-1">Discount Amount</label>
                                                    <input
                                                        type="number"
                                                        name="discount_amount"
                                                        id="discount_amount"
                                                        step="0.01"
                                                        min="0"
                                                        class="form-control form-control-sm"
                                                        value="{{ old('discount_amount', 0) }}"
                                                        placeholder="0.00"
                                                    >
                                                    <x-default.input-error name="discount_amount" />
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="paid_amount" class="form-label mb-1">Pay Amount</label>
                                                    <input
                                                        type="number"
                                                        name="paid_amount"
                                                        id="paid_amount"
                                                        step="0.01"
                                                        min="0"
                                                        max="{{ $total_due }}"
                                                        class="form-control form-control-sm"
                                                        value="{{ old('paid_amount', $total_due) }}"
                                                        placeholder="{{ number_format($total_due, 2) }}"
                                                    >
                                                    <x-default.input-error name="paid_amount" />
                                                </div>
                                                @if(!empty($extra_amount) && $extra_amount > 0)
                                                    <div class="col-md-4">
                                                        <label for="extra_return" class="form-label mb-1">Return Extra Amount</label>
                                                        <input
                                                            type="number"
                                                            name="extra_return"
                                                            id="extra_return"
                                                            step="0.01"
                                                            min="0"
                                                            max="{{ $extra_amount }}"
                                                            class="form-control form-control-sm"
                                                            value="{{ old('extra_return', $extra_amount) }}"
                                                            placeholder="{{ number_format($extra_amount, 2) }}"
                                                        >
                                                        <x-default.input-error name="extra_return" />
                                                    </div>
                                                @endif
                                            </div>
                                            <small class="text-muted d-block mt-2">System will automatically adjust discount and payment across all unpaid receipts.</small>
                                            <div class="mt-3">
                                                <button type="button" id="pay-due-submit" class="btn btn-success btn-sm">
                                                    <i class="fas fa-check-circle"></i> Apply &amp; Save
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="border rounded-3 p-3 h-100 bg-white shadow-sm">
                                        <h5 class="mb-3">Release Patient</h5>
                                        <form method="POST" action="{{ route('admin.admits.release', $admit->id) }}" id="release-form">
                                            @csrf
                                            <div class="form-group mb-3">
                                                <label for="release_at" class="form-label mb-1">Release Date &amp; Time</label>
                                                <input type="datetime-local" name="release_at" id="release_at" class="form-control form-control-sm" value="{{ now('Asia/Dhaka')->format('Y-m-d\\TH:i') }}">
                                                <x-default.input-error name="release_at" />
                                            </div>
                                            <button type="button" id="release-submit" class="btn btn-warning btn-sm w-100">
                                                <i class="fas fa-door-open"></i> Release Patient
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info" role="alert">
                                This admit was released on {{ $admit->release_at }}. Further changes are not allowed.
                            </div>
                        @endif

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="border rounded-3 p-3 bg-white shadow-sm h-100 d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h5 class="mb-0"><i class="fas fa-clinic-medical me-1 text-danger"></i> Hospital Cost</h5>
                                        <span class="badge bg-light text-dark">Admit ID: {{ $admit->id }}</span>
                                    </div>
                                    <p class="mb-3 small text-muted">
                                        Use this section to record any additional hospital-related costs for this admit.
                                    </p>
                                    <div class="mb-2">
                                        <span class="text-muted small">Total Hospital Cost</span>
                                        <div class="fw-bold fs-6">{{ number_format($hospital_cost_total, 2) }}</div>
                                    </div>

                                    @if($hospital_costs->count())
                                        <div class="mb-2">
                                            <span class="text-muted small d-block">Cost Details</span>
                                            <div class="small border rounded-3 p-2 bg-light" style="max-height: 160px; overflow-y: auto;">
                                                @foreach($hospital_costs as $cost)
                                                    <div class="mb-1">
                                                        @if($cost->creation_date)
                                                            <div class="text-muted small mb-1">{{ $cost->creation_date }}</div>
                                                        @endif
                                                        <div class="d-flex align-items-center">
                                                            <div class="text-truncate">
                                                                Reason: {{ $cost->reason ?: '-' }}
                                                            </div>
                                                            <div class="flex-grow-1 mx-1" style="border-bottom: 1px dotted #ccc;"></div>
                                                            <div class="fw-semibold whitespace-nowrap">
                                                                {{ number_format($cost->amount, 2) }} ৳
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if(!$loop->last)
                                                        <hr class="my-1">
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @elseif(!empty($hospital_cost_last_reason))
                                        <div class="mb-3">
                                            <span class="text-muted small d-block">Last Reason</span>
                                            <div class="small">{{ $hospital_cost_last_reason }}</div>
                                        </div>
                                    @endif
                                    <div class="mt-auto">
                                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#hospitalCostModal">
                                            <i class="fas fa-plus-circle"></i> Add / Update Hospital Cost
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- PC Payment is allowed even after release (only when refer exists) --}}
                            @if($admit->reefer)
                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 bg-white shadow-sm h-100 d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h5 class="mb-0"><i class="fas fa-user-md me-1 text-primary"></i> PC Payment</h5>
                                        @if($pcPayment)
                                            <span class="badge bg-success">Paid: {{ number_format($pcPayment->amount, 2) }}</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Not paid yet</span>
                                        @endif
                                    </div>
                                    <p class="mb-2 small text-muted">
                                        Manage payment for the referring doctor for this admit.
                                    </p>
                                    @if($pcPayment && $pcPayment->reason)
                                        <div class="mb-2">
                                            <span class="text-muted small d-block">Current Reason</span>
                                            <div class="small">{{ $pcPayment->reason }}</div>
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('admin.admits.pc-payment', $admit->id) }}" class="mt-auto">
                                        @csrf
                                        <input type="hidden" name="admit_id" value="{{ $admit->id }}">

                                        <div class="form-group mb-2">
                                            <label class="form-label mb-1">Refer Name</label>
                                            <input type="text" class="form-control form-control-sm" value="{{ $admit->reefer->name ?? 'N/A' }}" readonly>
                                        </div>

                                        <div class="form-group mb-2">
                                            <label class="form-label mb-1">Refer Phone</label>
                                            <input type="text" class="form-control form-control-sm" value="{{ $admit->reefer->phone ?? 'N/A' }}" readonly>
                                        </div>

                                        <div class="form-group mb-2">
                                            <label for="amount" class="form-label mb-1">Amount</label>
                                            <input
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                name="amount"
                                                id="amount"
                                                class="form-control form-control-sm"
                                                value="{{ old('amount', optional($pcPayment)->amount) }}"
                                                placeholder="Enter amount"
                                            >
                                            <x-default.input-error name="amount" />
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="reason" class="form-label mb-1">Reason</label>
                                            <textarea
                                                name="reason"
                                                id="reason"
                                                class="form-control form-control-sm"
                                                rows="3"
                                                placeholder="Enter reason"
                                            >{{ old('reason', optional($pcPayment)->reason) }}</textarea>
                                            <x-default.input-error name="reason" />
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-sm w-100">
                                            {{ $pcPayment ? 'Update PC Payment' : 'Pay PC' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="d-flex gap-2 mt-3">
                            <a href="{{ route('admin.admits.release.print', $admit->id) }}" target="_blank" class="btn btn-outline-primary">
                                <i class="fas fa-print"></i> Print Full Summary
                            </a>
                            <a href="{{ route('admin.admits.index') }}" class="btn btn-secondary">Back to Admits</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Hospital Cost Modal -->
<div class="modal fade" id="hospitalCostModal" tabindex="-1" aria-labelledby="hospitalCostModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="hospitalCostModalLabel">Add Hospital Cost</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.admits.hospital-cost', $admit->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-2">
                        <label for="hospital_cost_amount">Amount</label>
                        <input
                            type="number"
                            step="0.01"
                            min="0.01"
                            name="amount"
                            id="hospital_cost_amount"
                            class="form-control"
                            placeholder="Enter amount"
                            required
                        >
                    </div>
                    <div class="form-group mb-2">
                        <label for="hospital_cost_reason">Reason</label>
                        <textarea
                            name="reason"
                            id="hospital_cost_reason"
                            class="form-control"
                            rows="3"
                            placeholder="Enter reason (optional)"
                        ></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Save Hospital Cost</button>
                </div>
            </form>
        </div>
    </div>
</div>
                    @push('scripts')
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            @if(session('toast_message'))
                            Swal.fire({
                                toast: true,
                                icon: 'success',
                                title: @json(session('toast_message')),
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                            });
                            @endif

                            // Auto adjust pay amount when discount changes (only when there is due)
                            const totalDue = {{ $total_due }};
                            const extraAmount = {{ $extra_amount ?? 0 }};
                            const discountInput = document.getElementById('discount_amount');
                            const payInput = document.getElementById('paid_amount');
                            const extraReturnInput = document.getElementById('extra_return');

                            if (discountInput && payInput && totalDue > 0) {
                                const updatePayFromDiscount = () => {
                                    let discount = parseFloat(discountInput.value) || 0;
                                    if (discount < 0) discount = 0;
                                    if (discount > totalDue) discount = totalDue;

                                    const remaining = Math.max(totalDue - discount, 0);

                                    // Set max pay = remaining due after discount
                                    payInput.max = remaining.toFixed(2);

                                    // Always set pay amount to remaining (auto adjust)
                                    payInput.value = remaining.toFixed(2);
                                };

                                discountInput.addEventListener('input', updatePayFromDiscount);

                                // Initialize on load so fields are consistent
                                updatePayFromDiscount();
                            }

                            // Confirm pay-due in a toast-style popup
                            const payDueForm = document.getElementById('pay-due-form');
                            const payDueSubmit = document.getElementById('pay-due-submit');

                            if (payDueForm && payDueSubmit && typeof Swal !== 'undefined') {
                                payDueSubmit.addEventListener('click', function (e) {
                                    e.preventDefault();

                                    const discountVal = discountInput ? (parseFloat(discountInput.value) || 0) : 0;
                                    const payVal = payInput ? (parseFloat(payInput.value) || 0) : 0;
                                    const extraReturnVal = extraReturnInput ? (parseFloat(extraReturnInput.value) || 0) : 0;

                                    if (discountVal <= 0 && payVal <= 0 && extraReturnVal <= 0) {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Invalid payment input',
                                            html: '<p>Please enter a discount, pay amount, and/or extra return before submitting.</p>',
                                            confirmButtonText: 'OK',
                                        });
                                        return;
                                    }

                                    const remainingAfterDiscount = Math.max(totalDue - discountVal, 0);
                                    const dueAfterPayment = Math.max(remainingAfterDiscount - payVal, 0);

                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Confirm due payment',
                                        html: `<div style="text-align:left;font-size:13px;">`
                                            + `<div><strong>Total due:</strong> ${totalDue.toFixed(2)}</div>`
                                            + `<div><strong>Discount:</strong> ${discountVal.toFixed(2)}</div>`
                                            + `<div><strong>Pay amount:</strong> ${payVal.toFixed(2)}</div>`
                                            + `<div><strong>Return extra:</strong> ${extraReturnVal.toFixed(2)}</div>`
                                            + `<div><strong>Due after payment:</strong> ${dueAfterPayment.toFixed(2)}</div>`
                                            + `</div>`,
                                        showCancelButton: true,
                                        confirmButtonText: 'Yes, pay now',
                                        cancelButtonText: 'Cancel',
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            payDueForm.submit();
                                        }
                                    });
                                });
                            }

                            // Confirm release in a toast-style popup and warn if due remains
                            const releaseForm = document.getElementById('release-form');
                            const releaseSubmit = document.getElementById('release-submit');

                            if (releaseForm && releaseSubmit && typeof Swal !== 'undefined') {
                                releaseSubmit.addEventListener('click', function (e) {
                                    e.preventDefault();

                                    if (totalDue > 0) {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Cannot release patient',
                                            html: `<div style="text-align:left;font-size:13px;">`
                                                + `<div>There is still due remaining for this admit.</div>`
                                                + `<div><strong>Current due:</strong> ${totalDue.toFixed(2)}</div>`
                                                + `<div>Please clear all dues from the release payment section before releasing.</div>`
                                                + `</div>`,
                                            confirmButtonText: 'OK',
                                        });
                                        return;
                                    }

                                    const releaseAtInput = document.getElementById('release_at');
                                    const releaseTime = releaseAtInput && releaseAtInput.value
                                        ? releaseAtInput.value.replace('T', ' ')
                                        : 'now';

                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Confirm patient release',
                                        html: `<div style="text-align:left;font-size:13px;">`
                                            + `<div>After release, editing and deleting this admit will not be possible.</div>`
                                            + `<div><strong>Release time:</strong> ${releaseTime}</div>`
                                            + `</div>`,
                                        showCancelButton: true,
                                        confirmButtonText: 'Yes, release',
                                        cancelButtonText: 'Cancel',
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            releaseForm.submit();
                                        }
                                    });
                                });
                            }
                        });

                        // Generic delete helper for receipts from this page
                        function dataDelete(id, base_url) {
                            if (confirm('Are you sure you want to delete this receipt?')) {
                                let form = document.createElement('form');
                                form.method = 'POST';
                                form.action = base_url + '/' + id;
                                form.innerHTML = '@csrf @method("DELETE")';
                                document.body.appendChild(form);
                                form.submit();
                            }
                        }
                    </script>
                    @endpush
@endsection
