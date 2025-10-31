@extends('backend.layouts.master')
@section('title')
    List of {{ $pageHeader['title'] }}'s
@endsection
@push('styles')

@endpush
@section('admin-content')
    <!-- partial -->
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">

                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">{{ $pageHeader['title'] }}'s List</h4>
                            <form action="" method="GET" class="mb-4">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="start_date">Start Date:</label>
                                        <input type="date" name="start_date" id="start_date" class="form-control"
                                               value="{{ request('start_date') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="end_date">End Date:</label>
                                        <input type="date" name="end_date" id="end_date" class="form-control"
                                               value="{{ request('end_date') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="start_date">Refer by:</label>
                                        <select class="form-control" name="refer_id" id="select2">
                                            <option value="">Choose</option>
                                            @foreach($reffers as $item)
                                                <option
                                                    @selected(old('refer_id', request('refer_id')) == $item->id) value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="end_date">Export (PDF)</label>
                                        <select class="form-control" name="export" id="">
                                            <option value="">No</option>
                                            <option value="pdf">PDF</option>
                                            {{--                                            <option value="csv">CSV</option>--}}
                                        </select>
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <a href="{{ route('admin.reports.references') }}"
                                           class="btn btn-secondary ms-2">Reset</a>
                                    </div>
                                </div>
                            </form>
                            <p class="card-description">
                                @include('backend.layouts.partials.message')
                            </p>
                            <h5 class="float-end bg-success">{{ $startDate }} to {{ $endDate }}</h5>
                            <p>
                                <strong>Total:{{ $totalAmount }}</strong> ||
                                <strong>Paid:{{ $totalPaidAmount }}</strong> ||
                                <strong>Due:{{ $totalDueAmount }}</strong>
                                {{--                                <a href="?pay=yes">Pay</a>--}}
                                @if(request('refer_id'))
                                    <strong class="text-danger">**Sms available if your account active for sms system
                                        automatic send it when you pay due for specific person !
                                    </strong>
                                @endif
                            <p><strong>Total Selected Amount:</strong> <span id="total_amount_by_checkin">0</span> ৳</p>

                            <span class="row">
                                    <span class="col-md-3">
                                        <input type="hidden" value="{{ request('refer_id') }}" id="refer_id">

                                    </span>
                                    <span class="col-md-3">
                                          <select class="form-select" name="payment_type"
                                                  id="payment_type" required>
                                    <option value="" disabled selected>Select a method
                                    </option>
                                    @foreach(\App\Models\Invoice::$paymentArray as $type)
                                                  <option value="{{ $type }}">{{ $type }}</option>
                                              @endforeach
                                </select>
                                    </span>
                                    <span class="col-md-3">
                                                                        <button type="submit" class="btn btn-info"
                                                                                id="payNow">Pay Now</button>

                                    </span>
                                </span>

                            </p>
                            <div class="table-responsive">
                                <table class="table table-striped mt-3">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th>Invoice</th>
                                        <th>Refer By</th>
                                        <th>Dr</th>
                                        <th>Patient</th>
                                        <th>Amount</th>
                                        <th>Paid</th>
                                        <th>Unpaid</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($datas as $item)
                                        <tr>
                                            <td>

                                                @if($item->refer_fee_total - ($item->costs->sum('amount'))>0)
                                                    @if(($item->total_amount - $item->paid_amount_sum_paid_amount)>0)
                                                        <span>X</span>
                                                    @else
                                                    <input type="checkbox" class="invoice-check"
                                                           value="{{ $item->id }}">

                                                    <input type="hidden" class="refer_id" value="{{ $item->refer_id }}">
                                                    <input type="hidden" class="invoice_id" value="{{ $item->id }}">
                                                    <input type="hidden" class="amount"
                                                           value="{{ $item->refer_fee_total-($item->costs->sum('amount')) }}">

                                                    @endif
                                                @endif
                                            </td>
                                            <td @if(($item->total_amount - $item->paid_amount_sum_paid_amount)>0) class="text-danger" @endif>{{ $item->id }} | {{ $item->invoice_number }} (Due: {{ ($item->total_amount - $item->paid_amount_sum_paid_amount) }})
                                                <br>
                                                {{ $item->creation_date }}
                                            </td>
                                            <td>{{ $item->reeferBy->name ?? 'n/a' }}</td>
                                            <td>{{ $item->reeferDr->name ?? 'n/a' }}</td>
                                            <td>
                                                {{ $item->patient_name }}
                                                <br>
                                                <strong>Total:</strong>{{ $item->total_amount+$item->discount_amount }}</br>
                                                <strong>Less:</strong>{{ $item->discount_amount }}
                                            </td>
                                            <td>{{ $item->refer_fee_total  }}</td>
                                            <td>{{ $item->costs->sum('amount') }}</td>
                                            <td>{{ $item->refer_fee_total - ($item->costs->sum('amount')) }} @if($item->refer_fee_total - ($item->costs->sum('amount'))<0)
                                                    Extra
                                                @endif</td>
                                            <td>
                                                @if($item->refer_fee_total - ($item->costs->sum('amount'))>0)
                                                    <strong class="badge bg-danger">Pending</strong>
                                                    <a class="badge bg-info"
                                                       href="{{ route('admin.invoices.show',$item->id) }}"><i
                                                            class="fas fa-pen"></i></a>
                                                @else
                                                    <strong class="badge bg-info">Paid</strong>
                                                @endif
                                            </td>

                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
    <!-- main-panel ends -->
@endsection
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {

            $('#select2').select2({
                placeholder: "Select reagents",
                allowClear: true
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const checkboxes = document.querySelectorAll('.invoice-check');
            const payNowBtn = document.getElementById('payNow');
            const paymentTypeSelect = document.getElementById('payment_type');
            const referId = document.getElementById('refer_id');
            const totalDisplay = document.getElementById('total_amount_by_checkin');

            function updateTotalAmount() {
                let total = 0;
                document.querySelectorAll('.invoice-check:checked').forEach(checkedBox => {
                    const row = checkedBox.closest('tr');
                    const amount = parseFloat(row.querySelector('.amount').value) || 0;
                    total += amount;
                });
                totalDisplay.textContent = total.toFixed(2);
            }

            // Update total on every checkbox toggle
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateTotalAmount);
            });
            payNowBtn.addEventListener('click', async (e) => {
                const selectedData = [];

                document.querySelectorAll('.invoice-check:checked').forEach(checkedBox => {
                    const row = checkedBox.closest('tr');
                    selectedData.push({
                        invoice_id: row.querySelector('.invoice_id').value,
                        refer_id: row.querySelector('.refer_id').value,
                        amount: row.querySelector('.amount').value
                    });
                });

                const paymentType = paymentTypeSelect.value;
                const referIdGet = referId.value;

                if (!referIdGet) {
                    alert("Please select a Refer by.");
                    return;
                }

                if (!paymentType) {
                    alert("Please select a payment method.");
                    return;
                }

                if (selectedData.length === 0) {
                    alert("Please select at least one invoice.");
                    return;
                }

                const response = await fetch("{{ route('admin.cost.store-multiple') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        payment_type: paymentType,
                        refer_id: referIdGet,
                        invoices: selectedData
                    })
                });

                const result = await response.json();

                console.log('Success:', result);
                if (response.ok) {
                    alert('Payment submitted successfully!');
                    location.reload(); // or redirect, or reset form
                } else {
                    console.error('Server Error:', result);
                    alert('There was a problem submitting the payment.');
                }

            });
        });
    </script>

@endpush
