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

                            <div class="table-responsive">
                                <table class="table table-striped mt-3">
                                    <thead>
                                    <tr>
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
                                <div class="d-flex justify-content-end">
                                    {!! $datas->appends(request()->query())->links() !!}
                                </div>
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

@endpush
