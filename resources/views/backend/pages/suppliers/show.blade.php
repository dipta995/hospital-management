@extends('backend.layouts.master')
@section('title')
    {{ $pageHeader['title'] }} Details
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
                            <h4 class="card-title">Payment</h4>
                            <div class="row">
                                <div class="col-md-2">
                                    <a class="btn btn-info" data-bs-toggle="modal"
                                       data-bs-target="#referPaymentModal">Payment</a>
                                </div>

                                <!-- Bootstrap Modal -->
                                <div class="modal fade" id="referPaymentModal" tabindex="-1"
                                     aria-labelledby="referPaymentModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <!-- Modal Header -->
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="referPaymentModalLabel">Add Salary</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                            </div>
                                            <!-- Modal Body -->
                                            <form method="post"
                                                  action="{{ route('admin.employees.salary',$singleData->id) }}">
                                                @csrf
                                                <div class="modal-body row">

                                                    <div class="mb-3 col-md-6">
                                                        <label for="month" class="form-label">Month(Choose Salary Month)</label>
                                                        <select class="form-select" name="month"
                                                                id="month" required>
                                                            <option value="" disabled selected>Select what was the month</option>
                                                            @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                                                <option value="{{ $month }}"
                                                                        @if($month == ucfirst(date("F"))) selected @endif>{{ $month }}</option>
                                                            @endforeach

                                                        </select>
                                                    </div>
                                                    <div class="mb-3 col-md-6">
                                                        <label for="year" class="form-label">Choose Year</label>
                                                        <select class="form-select" name="year"
                                                                id="year" required>
                                                            <option value="" disabled selected>Select what was the year</option>
                                                            <option value="{{ date('Y')-1 }}">{{ date('Y')-1 }}</option>
                                                            <option selected
                                                                    value="{{ date('Y') }}">{{ date('Y') }}</option>
                                                            <option value="{{ date('Y')+1 }}">{{ date('Y')+1 }}</option>

                                                        </select>
                                                    </div>
                                                    <div class="mb-3 col-md-6">
                                                        <label for="salary" class="form-label">Amount you want to pay</label>
                                                        <input type="number" class="form-control" name="salary"
                                                               id="salary"
                                                               value="{{ $singleData->salary }}"
                                                               required>
                                                        @if ($errors->has('salary'))
                                                            <div class="alert alert-danger">
                                                                {{ $errors->first('salary') }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="mb-3 col-md-6">
                                                        <label for="note" class="form-label">Note(Reason)</label>
                                                        <input type="text" class="form-control" name="note"
                                                               id="note"
                                                               value="{{ $singleData->note }}">
                                                        @if ($errors->has('note'))
                                                            <div class="alert alert-danger">
                                                                {{ $errors->first('note') }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="mb-3 col-md-6">
                                                        <label for="refer_by" class="form-label">Paid By</label>
                                                        <input type="text" class="form-control" name="refer_by"
                                                               id="refer_by"
                                                               value="{{ auth()->user()->name }}">
                                                        @if ($errors->has('refer_by'))
                                                            <div class="alert alert-danger">
                                                                {{ $errors->first('refer_by') }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="mb-3 col-md-6">
                                                        <label for="paymentMethod" class="form-label">Payment
                                                            Method</label>
                                                        <select class="form-select" name="payment_type"
                                                                id="payment_type" required>
                                                            <option value="" disabled selected>Select a method
                                                            </option>
                                                            @foreach(\App\Models\Invoice::$paymentArray as $item)
                                                                <option value="{{ $item }}">{{ $item }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
{{--                                                    <div class="mb-3 col-md-12">--}}
{{--                                                        <label for="created_at" class="form-label">Paid At</label>--}}
{{--                                                        <input type="datetime-local" class="form-control" name="created_at"--}}
{{--                                                               id="created_at">--}}
{{--                                                        @if ($errors->has('created_at'))--}}
{{--                                                            <div class="alert alert-danger">--}}
{{--                                                                {{ $errors->first('created_at') }}--}}
{{--                                                            </div>--}}
{{--                                                        @endif--}}
{{--                                                    </div>--}}

                                                </div>
                                                <!-- Modal Footer -->
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">
                                                        Close
                                                    </button>
                                                    <button type="submit" class="btn btn-primary">
                                                        Pay
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <p class="card-description">
                                @include('backend.layouts.partials.message')
                            </p>
                            <div class="table-responsive">

                                <table class="table table-striped mt-3">
                                    <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Year</th>
                                        <th>Amount</th>
                                        <th>Payment Date</th>
                                        <th>Payment Type</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($singleData->employeeSalaries as $item)
                                        <tr>
                                            <td>{{ $item->month }}</td>
                                            <td>{{ $item->year }}</td>
                                            <td>{{ $item->salary }} TK</td>
                                            <td>{{ $item->created_at }} </td>
                                            <td class="badge bg-success text-white">
                                                {{ $item->payment_type }}
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

@push('scripts')

@endpush
