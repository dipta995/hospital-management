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
                                <h5>Receipts List</h5>
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
                                    <h5>Pay Due (Global)</h5>
                                    <p class="text-muted mb-2">
                                        Total due for this admit: <strong>{{ number_format($total_due, 2) }}</strong>.
                                        To clear all dues, pay this full amount.
                                    </p>
                                    <form method="POST" action="{{ route('admin.admits.pay-due', $admit->id) }}">
                                        @csrf
                                        <div class="form-group mb-1">
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
                                        <small class="text-muted d-block mb-2">System will automatically adjust this payment across all unpaid receipts.</small>
                                        <button type="submit" class="btn btn-success">Pay Due</button>
                                    </form>
                                </div>
                                <div class="col-md-3">
                                    <h5>Release Patient</h5>
                                    <form method="POST" action="{{ route('admin.admits.release', $admit->id) }}">
                                        @csrf
                                        <div class="form-group mb-2">
                                            <label for="release_at">Release Date & Time</label>
                                            <input type="datetime-local" name="release_at" id="release_at" class="form-control" value="{{ now('Asia/Dhaka')->format('Y-m-d\TH:i') }}">
                                            <x-default.input-error name="release_at" />
                                        </div>
                                        <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to release this admit? After release, editing and deleting will not be possible.')">Release</button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info" role="alert">
                                This admit was released on {{ $admit->release_at }}. Further changes are not allowed.
                            </div>
                        @endif

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
                                        <input
                                            type="text"
                                            name="reason"
                                            id="reason"
                                            class="form-control"
                                            value="{{ old('reason', optional($pcPayment)->reason) }}"
                                            placeholder="Enter reason"
                                        >
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
                        });
                    </script>
                    @endpush
@endsection
