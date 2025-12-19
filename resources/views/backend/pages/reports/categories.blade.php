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
                                    <div class="col-md-3">
                                        <label for="start_date">Choose Category</label>
                                              <select class="form-control" name="category_id" id="category_id">
                                                <option value="">--Choose--</option>
                                                @foreach($categories as $item)
                                                    <option @selected(old('category_id', request('category_id')) == $item->id) value="{{ $item->id }}">{{ $item->name }}</option>
                                                @endforeach
                                            </select>

                                    </div>
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
                                    <div class="col-md-2">
                                        <label for="start_date">Doctor:</label>
                                        <select class="form-control" name="dr_refer_id" id="select2">
                                            <option value="">Choose</option>
                                            @foreach($reffers as $item)
                                                <option
                                                    @selected(old('dr_refer_id', request('dr_refer_id')) == $item->id) value="{{ $item->id }}">{{ $item->name }}</option>
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
                                        <a href="" class="btn btn-secondary ms-2">Reset</a>
                                    </div>
                                </div>
                            </form>

                            <p class="card-description">
                                @include('backend.layouts.partials.message')
                            </p>
                            <div class="table-responsive">
                                <table class="table table-striped mt-3">
                                        <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th>Product Name</th>
                                            <th>Invoice(Id)</th>
                                            <th>Doctor</th>
                                            <th>After Discount</th>
                                            <th>Creation Date</th>
                                            <th>Amount</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($datas as $categoryName => $categoryData)
                                            <tr>
                                                <td colspan="5" style="background-color: #f2f2f2; font-weight: bold;">
                                                    {{ $categoryName }} (Total Items: {{ $categoryData['total_count'] }})
                                                </td>
                                            </tr>
                                            @foreach ($categoryData['invoices'] as $invoiceList)
                                                <tr>
                                                    <td></td> <!-- Empty cell for alignment -->
                                                    <td>{{ $invoiceList->product->name }}</td>
                                                    <td>{{ $invoiceList->invoice->invoice_number }}({{ $invoiceList->invoice->patient_no }})</td>
                                                    <td>{{ $invoiceList->invoice->reeferDr->name }}</td>
                                                    <td>{{ $invoiceList->price - $invoiceList->discount_price }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($invoiceList->created_at)->format('d F Y') }}
                                                        </td>
                                                    <td>{{ $invoiceList->price }} TK.</td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td colspan="5" style="text-align: right; font-weight: bold;">Total for {{ $categoryName }}:</td>
                                                <td style="font-weight: bold;">{{ $categoryData['total_price']- $categoryData['discount_price'] }} TK.</td>
                                                <td></td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                <div class="d-flex justify-content-end">
{{--                                    {!! $datas->appends(request()->query())->links() !!}--}}
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
