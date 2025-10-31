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
                            <form method="GET" action="">
                                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                    <div>
                                        <label>From Date:</label>
                                        <input type="date" class="form-control" name="from_date" value="{{ request('from_date') }}">
                                    </div>
                                    <div>
                                        <label>To Date:</label>
                                        <input type="date" class="form-control" name="to_date" value="{{ request('to_date') }}">
                                    </div>
                                    <div>
                                        <label>PDF:</label>
                                    <select name="pdf" class="form-control" id="">
                                        <option value="no">No</option>
                                        <option value="yes">Yes</option>
                                    </select>
                                    </div>
                                    <div style="align-self: end;">
                                        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                                    </div>
                                </div>
                            </form>

                            <br>
                            {!! currentBalanceMonth() !!}
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
