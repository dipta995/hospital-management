@extends('backend.layouts.master')
@section('title')
    Preview {{ $pageHeader['title'] }}
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
                            <h4 class="card-title">{{ $pageHeader['title'] }}'s Details</h4>
                            <div class="row">
                                @if($singleData->refer_fee_total - ($singleData->costs->sum('amount'))>0)
                                    <div class="col-md-2">
                                        <a class="btn btn-info" data-bs-toggle="modal"
                                           data-bs-target="#referPaymentModal">Refer Payment</a>
                                    </div>

                                    <!-- Bootstrap Modal -->
                                    <div class="modal fade" id="referPaymentModal" tabindex="-1"
                                         aria-labelledby="referPaymentModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <!-- Modal Header -->
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="referPaymentModalLabel">Refer
                                                        Payment</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                </div>
                                                <!-- Modal Body -->
                                                <form method="post" action="{{ route('admin.costs.store') }}">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <input type="hidden" name="pc_payment" value="pc_payment">

                                                        <div class="mb-3">
                                                            <label for="referName" class="form-label">Refer Name</label>
                                                            <input type="text" readonly
                                                                   value="{{ $singleData->reeferBy->name ?? 'N/A' }}"
                                                                   class="form-control" name="reefer_name"
                                                                   id="name"
                                                                   required>
                                                            <input type="hidden" class="form-control"
                                                                   value="{{ $singleData->refer_id }}" name="refer_id"
                                                                   id="refer_id" required>
                                                            <input type="hidden" class="form-control" name="invoice_id"
                                                                   id="invoice_id"
                                                                   value="{{ $singleData->id }}" required>

                                                            <input type="hidden" class="form-control" name="reason"
                                                                   id="reason"
                                                                   value=" Refer Payment" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="amount" class="form-label">Amount</label>
                                                            <input type="number" class="form-control" name="amount"
                                                                   id="amount"
                                                                   value="{{ $singleData->refer_fee_total - ($singleData->costs->sum('amount')) }}"
                                                                   required>
                                                            @if ($errors->has('amount'))
                                                                <div class="alert alert-danger">
                                                                    {{ $errors->first('amount') }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="account_no" class="form-label">Account No
                                                                Date</label>
                                                            <input type="text" class="form-control" id="account_no"
                                                                   name="account_no"
                                                                   required>
                                                        </div>
                                                        <div class="mb-3">
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

                                @endif
                                <div class="col-md-3">
                                    <p class="bg-danger text-white text-center">Delivery Complete Status </p>
                                    <div class="form-check form-switch float-end">
                                        <input onclick="activeData({{ $singleData->id }},'/admin/invoices')"
                                               {{ $singleData->status==\App\Models\Invoice::$deliveryStatusArray[1] ? 'checked' : '' }}
                                               class="form-check-input" name="" type="checkbox"
                                               id="status_switch{{ $singleData->id }}">
                                    </div>
                                </div>
                            </div>
                            <p class="card-description">
                                @include('backend.layouts.partials.message')
                            </p>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>Field</th>
                                        <th>Details</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <th>Invoice:</th>
                                        <td>{{ $singleData->invoice_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>Patient Name:</th>
                                        <td>{{ $singleData->patient_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Patient Phone:</th>
                                        <td>{{ $singleData->patient_phone }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                                <h4 class="btn btn-danger">Note:</h4>
                                <p class="bg-info text-white">{{ $singleData->note ?? "N/A" }}</p>
                                <table class="table table-striped mt-3">
                                    <thead>
                                    <tr>
                                        <th>Test Name</th>
                                        <th>price</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($singleData->invoiceList as $item)
                                        <tr>
                                            <td>{{ $item->product->name }}</td>
                                            <td>{{ $item->price }}</td>
                                            <td>
                                                <span class="badge
                                                 @if($item->status==\App\Models\InvoiceList::$statusArray[0])
                                                 bg-danger
                                                 @elseif($item->status==\App\Models\InvoiceList::$statusArray[1])
                                                bg-warning
                                                 @elseif($item->status==\App\Models\InvoiceList::$statusArray[2])
                                                 bg-info
                                                 @else
                                                 bg-dark
                                                @endif
                                                ">
                                                    {{ $item->status }}

                                                </span>
                                                @if($item->status==\App\Models\InvoiceList::$statusArray[2])
                                                    @if($item->document != null)
                                                        <a class="badge bg-danger" target="_blank"
                                                           href="{{ route('admin.lab.report.file-download',$item->id) }}"><i
                                                                class="fas fa-download"></i></a>
                                                    @endif
                                                @endif
                                                @if($item->status==\App\Models\InvoiceList::$statusArray[2])
                                                    @if($item->test_report != null)
                                                        <a class="badge bg-info" target="_blank"
                                                           href="{{ route('admin.lab.report.pdf-preview',$item->id) }}"><i
                                                                class="fas fa-download"></i></a>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                {{--                                Test Report For Download --}}
                                <table class="table table-striped mt-3">
                                    <thead>
                                    <tr>
                                        <th>Test Name</th>
                                        <th>Export</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($reports as $item)
                                        <tr>
                                            <td>{{ $item->reportDemo->name }}</td>
                                            <td>
                                                <a class="badge bg-danger" target="_blank"
                                                   href="{{ route('admin.preview-pdf-report',$item->id) }}"><i
                                                        class="fas fa-download"></i></a>
                                            </td>

                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>


                                {{--                                Test Report For Download --}}
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
