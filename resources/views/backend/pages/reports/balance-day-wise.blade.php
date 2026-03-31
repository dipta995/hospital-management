@extends('backend.layouts.master')
@section('title')
    Day Wise Balance Report
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
                            <h4 class="card-title mb-3">Day Wise Balance Report</h4>
                            <form method="GET" action="">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-3 col-sm-6">
                                        <label class="form-label">From Date</label>
                                        <input type="date" class="form-control" name="from_date" value="{{ request('from_date') }}">
                                    </div>
                                    <div class="col-md-3 col-sm-6">
                                        <label class="form-label">To Date</label>
                                        <input type="date" class="form-control" name="to_date" value="{{ request('to_date') }}">
                                    </div>
                                    <div class="col-md-2 col-sm-4">
                                        <label class="form-label">Export (PDF)</label>
                                        <select name="pdf" class="form-control">
                                            <option value="no" {{ request('pdf') == 'no' ? 'selected' : '' }}>No</option>
                                            <option value="yes" {{ request('pdf') == 'yes' ? 'selected' : '' }}>Yes</option>
                                        </select>
                                    </div>
                                    <div class="col-auto mt-2 mt-md-0">
                                        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                                    </div>
                                </div>
                            </form>

                            <hr class="my-3">
                            {!! currentBalanceDayWise() !!}
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
