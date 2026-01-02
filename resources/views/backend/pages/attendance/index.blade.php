@extends('backend.layouts.master')
@section('title')
    Attendance Summary
@endsection
@push('styles')
@endpush
@section('admin-content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Attendance Summary</h4>
                        <form method="get" class="row mb-3">
                            @if(!empty($employeeId))
                                <input type="hidden" name="employee_id" value="{{ $employeeId }}" />
                            @endif
                            <div class="col-md-3">
                                <label for="month" class="form-label">Month</label>
                                <select class="form-select" name="month" id="month">
                                    @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                        <option value="{{ $month }}" @if($month == request('month', now()->format('F'))) selected @endif>{{ $month }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="year" class="form-label">Year</label>
                                <select class="form-select" name="year" id="year">
                                    @for($y = date('Y')-2; $y <= date('Y')+1; $y++)
                                        <option value="{{ $y }}" @if($y == request('year', now()->year)) selected @endif>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-3 mt-4">
                                <button type="submit" class="btn btn-info">Filter</button>
                                <a href="?pdf=1" class="btn btn-primary">PDF</a>
                                <a href="?print=1" class="btn btn-secondary" target="_blank">Print</a>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Employee</th>
                                        <th>Date</th>
                                        <th>In Time</th>
                                        <th>Out Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($attendances as $attendance)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ optional($attendance->employee)->name ?? '-' }}</td>
                                            <td>{{ $attendance->date }}</td>
                                            <td>{{ $attendance->in_time ? \Carbon\Carbon::parse($attendance->in_time)->format('H:i:s') : '-' }}</td>
                                            <td>{{ $attendance->out_time ? \Carbon\Carbon::parse($attendance->out_time)->format('H:i:s') : '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5">No attendance found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-end">
                                {!! $attendances->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
@endpush
