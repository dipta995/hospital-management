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
                        <h4 class="card-title">Admit Release Summary</h4>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Patient Details</h5>
                                <p><strong>Name:</strong> {{ $admit->user->name ?? 'N/A' }}</p>
                                <p><strong>Phone:</strong> {{ $admit->user->phone ?? 'N/A' }}</p>
                                <p><strong>Age:</strong> {{ $admit->user->age ?? 'N/A' }}</p>
                                <p><strong>Gender:</strong> {{ $admit->user->gender ?? 'N/A' }}</p>
                                <p><strong>Address:</strong> {{ $admit->user->address ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5>Admit Details</h5>
                                <p><strong>Admit Date:</strong> {{ $admit->admit_at ?? 'N/A' }}</p>
                                <p><strong>Release Date:</strong> {{ $admit->release_at ?? 'Not released' }}</p>
                                <p><strong>Bed/Cabin:</strong> {{ $admit->bed_or_cabin ?? 'N/A' }}</p>
                                <p><strong>Father/Spouse:</strong> {{ $admit->father_or_spouse ?? 'N/A' }}</p>
                                <p><strong>Received By:</strong> {{ $admit->received_by ?? 'N/A' }}</p>
                                <p><strong>Refer:</strong>
                                    {{ $admit->reefer->name ?? 'N/A' }}
                                    @if(!empty($admit->reefer?->phone))
                                        ({{ $admit->reefer->phone }})
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5>Receipt Summary (This Admit)</h5>
                                <div class="row">
                                    <div class="col-md-3">
                                        <p><strong>Total Amount:</strong> {{ number_format($total_amount, 2) }}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <p><strong>Total Discount:</strong> {{ number_format($total_discount, 2) }}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <p><strong>Net Amount:</strong> {{ number_format($net_total, 2) }}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <p><strong>Total Paid:</strong> {{ number_format($total_paid, 2) }}</p>
                                        <p><strong>Total Due:</strong> {{ number_format($total_due, 2) }}</p>
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
                                                <th>Discount</th>
                                                <th>Net</th>
                                                <th>Paid</th>
                                                <th>Due</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($receipts as $recept)
                                                @php
                                                    $net = $recept->total_amount - $recept->discount_amount;
                                                    $paid = $recept->receptPayments->sum('paid_amount');
                                                    $due = max($net - $paid, 0);
                                                @endphp
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $recept->created_date }}</td>
                                                    <td>{{ number_format($recept->total_amount, 2) }}</td>
                                                    <td>{{ number_format($recept->discount_amount, 2) }}</td>
                                                    <td>{{ number_format($net, 2) }}</td>
                                                    <td>{{ number_format($paid, 2) }}</td>
                                                    <td>{{ number_format($due, 2) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">No receipts found for this admit.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        @if(!$admit->release_at)
                            <div class="row mb-4">
                                <div class="col-md-6">
                                        <h5>Release Payment (Global)</h5>
                                        <p class="text-muted mb-2">
                                            Total due for this admit: <strong>{{ number_format($total_due, 2) }}</strong>.
                                            You can give a discount and/or take payment now.
                                        </p>
                                        <form method="POST" action="{{ route('admin.admits.pay-due', $admit->id) }}" id="pay-due-form">
                                            @csrf
                                            <div class="form-group mb-1">
                                                <label for="discount_amount">Discount Amount</label>
                                                <input
                                                    type="number"
                                                    name="discount_amount"
                                                    id="discount_amount"
                                                    step="0.01"
                                                    min="0"
                                                    max="{{ $total_due }}"
                                                    class="form-control"
                                                    value="{{ old('discount_amount', 0) }}"
                                                    placeholder="e.g. 0.00"
                                                >
                                                <x-default.input-error name="discount_amount" />
                                            </div>
                                            <div class="form-group mb-1 mt-2">
                                                <label for="paid_amount">Pay Amount</label>
                                                <input
                                                    type="number"
                                                    name="paid_amount"
                                                    id="paid_amount"
                                                    step="0.01"
                                                    min="0"
                                                    max="{{ $total_due }}"
                                                    class="form-control"
                                                    value="{{ old('paid_amount', $total_due) }}"
                                                    placeholder="e.g. {{ number_format($total_due, 2) }}"
                                                >
                                                <x-default.input-error name="paid_amount" />
                                            </div>
                                            <small class="text-muted d-block mb-2">System will automatically adjust discount and payment across all unpaid receipts.</small>
                                            <button type="button" id="pay-due-submit" class="btn btn-success">Apply &amp; Save</button>
                                        </form>
                                    </div>
                                <div class="col-md-3">
                                    <h5>Release Patient</h5>
                                    <form method="POST" action="{{ route('admin.admits.release', $admit->id) }}" id="release-form">
                                        @csrf
                                        <div class="form-group mb-2">
                                            <label for="release_at">Release Date &amp; Time</label>
                                            <input type="datetime-local" name="release_at" id="release_at" class="form-control" value="{{ now('Asia/Dhaka')->format('Y-m-d\TH:i') }}">
                                            <x-default.input-error name="release_at" />
                                        </div>
                                        <button type="button" id="release-submit" class="btn btn-warning">Release</button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info" role="alert">
                                This admit was released on {{ $admit->release_at }}. Further changes are not allowed.
                            </div>
                        @endif

                        <div class="row mb-4">
                            <div class="col-md-3 offset-md-9">
                                <h5>Hospital Cost</h5>
                                <p><strong>Total Hospital Cost:</strong> {{ number_format($hospital_cost_total, 2) }}</p>
                                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#hospitalCostModal">
                                    Add / Update Hospital Cost
                                </button>
                            </div>
                        </div>

                        {{-- PC Payment is allowed even after release (only when refer exists) --}}
                        @if($admit->reefer)
                        <div class="row mb-4">
                            <div class="col-md-3 offset-md-9">
                                <h5>PC Payment</h5>
                                <form method="POST" action="{{ route('admin.admits.pc-payment', $admit->id) }}">
                                    @csrf
                                    <input type="hidden" name="admit_id" value="{{ $admit->id }}">

                                    <div class="form-group mb-2">
                                        <label>Refer Name</label>
                                        <input type="text" class="form-control" value="{{ $admit->reefer->name ?? 'N/A' }}" readonly>
                                    </div>

                                    <div class="form-group mb-2">
                                        <label>Refer Phone</label>
                                        <input type="text" class="form-control" value="{{ $admit->reefer->phone ?? 'N/A' }}" readonly>
                                    </div>

                                    @if($pcPayment)
                                        <div class="alert alert-success py-1 mb-2">
                                            <small>Already paid PC: <strong>{{ number_format($pcPayment->amount, 2) }}</strong></small>
                                        </div>
                                    @endif

                                    <div class="form-group mb-2">
                                        <label for="amount">Amount</label>
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            name="amount"
                                            id="amount"
                                            class="form-control"
                                            value="{{ old('amount', optional($pcPayment)->amount) }}"
                                            placeholder="Enter amount"
                                        >
                                        <x-default.input-error name="amount" />
                                    </div>

                                    <div class="form-group mb-2">
                                        <label for="reason">Reason</label>
                                        <textarea
                                            name="reason"
                                            id="reason"
                                            class="form-control"
                                            rows="3"
                                            placeholder="Enter reason"
                                        >{{ old('reason', optional($pcPayment)->reason) }}</textarea>
                                        <x-default.input-error name="reason" />
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        {{ $pcPayment ? 'Update PC Payment' : 'Pay PC' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endif

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

                            // Auto adjust pay amount when discount changes
                            const totalDue = {{ $total_due }};
                            const discountInput = document.getElementById('discount_amount');
                            const payInput = document.getElementById('paid_amount');

                            if (discountInput && payInput) {
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

                                    if (discountVal <= 0 && payVal <= 0) {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Invalid payment input',
                                            html: '<p>Please enter a discount and/or pay amount before submitting.</p>',
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
                    </script>
                    @endpush
@endsection
