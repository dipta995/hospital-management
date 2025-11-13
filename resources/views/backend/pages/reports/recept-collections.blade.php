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
                                        <label for="start_date">Start Date:</label>
                                        <input type="date" name="start_date" id="start_date" class="form-control"
                                               value="{{ request('start_date') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="end_date">End Date:</label>
                                        <input type="date" name="end_date" id="end_date" class="form-control"
                                               value="{{ request('end_date') }}">
                                    </div>
                                    <div class="col-md-3">
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
{{--                            <p class="text-end"><span--}}
{{--                                    class="bg-info text-white p-1 ">Collection : <span>{{ $total_collection-$total_due }} </span></span>--}}
{{--                                | <span class="bg-danger text-white p-1 "> Due : <span>{{ $total_due }}</span></span>--}}
{{--                                | <span class="bg-danger text-white p-1 "> Refer Due : <span>{{ $refer_amount_total- $refer_amount_paid }}</span></span>--}}
{{--                            </p>--}}
                            <p class="card-description">
                                @include('backend.layouts.partials.message')
                            </p>
                            <div class="table-responsive">
                                <table class="table table-striped mt-3">
                                    <thead>

                                    </thead>
                                    <tbody>
                                    @foreach($datas as $date => $invoices)
                                        <tr class="table-info">
                                            <td colspan="3"><strong>Date: {{ $date }}</strong> </td>
                                            <td><strong>TOTAL:</strong></td>
                                            <td colspan="2"><strong>Subtotals: {{ $invoices->sum('total_amount') +  $invoices->sum('total_discount') }}</strong></td>
                                            <td colspan="1"><strong>Discounts: {{ $invoices->sum('total_discount') }}</strong></td>
                                            <td colspan="2"><strong>Collection: {{ $invoices->sum('total_collection') }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>#</th>
                                            <th>Sub Total</th>
                                            <th>Discount</th>
                                            <th>Collection</th>
                                            <th>Doctor</th>
                                            <th>Refer</th>
                                        </tr>

                                        @foreach($invoices as $invoice_id => $group)
                                            @php
                                                $invoice = isset($group['data']) ? collect($group['data'])->first()->recept ?? null : null;
                                            @endphp
                                            <tr class="table-warning">
                                                <td colspan="8"><strong>Recept: {{ $invoice->id ?? 'N/A' }}</strong></td>
                                            </tr>

                                            @if(isset($group['data']))
                                                @foreach($group['data'] as $index => $item)
                                                    <tr>
                                                        <td>{{ $index + 1 }}
                                                            @foreach($invoice->services?? [] as $pr)
                                                                {{ $pr->service->name }}
                                                            @endforeach

                                                        </td>
                                                        <td>{{ ($invoice->total_amount ?? 0) + ($invoice->discount_amount ?? 0) }}</td>
                                                        <td>{{ $invoice->discount_amount ?? 0 }}</td>
                                                        <td>{{ $item->paid_amount }}</td>
                                                        <td>{{ $invoice->admit->drreefer->name ?? 'N/A' }}</td>
                                                        <td>{{ $invoice->admit->reefer->name ?? 'N/A' }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        @endforeach
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
