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
                            <div class="col-md-3">
                                <label for="export" class="form-label">Generate PDF</label>
                                <select class="form-select" name="export" id="export">
                                    <option value="">No</option>
                                    <option value="pdf" @if(request('export')=='pdf') selected @endif>Yes</option>
                                </select>
                            </div>
                            <div class="col-md-3 mt-4">
                                <button type="submit" class="btn btn-info">Submit</button>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Total Present</th>
                                        <th>Employee</th>
                                        <th>In Time</th>
                                        <th>Out Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php use Carbon\Carbon; @endphp
                                    @forelse($groupedAttendances ?? [] as $date => $items)
                                        <tr class="table-secondary">
                                            <td><strong>{{ Carbon::parse($date)->format('d M Y') }}</strong></td>
                                            <td><strong>{{ $items->count() }}</strong></td>
                                            <td colspan="3"></td>
                                        </tr>
                                        @foreach($items as $attendance)
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td>{{ optional($attendance->employee)->name ?? '-' }}</td>
                                                <td>{{ $attendance->in_time ? Carbon::parse($attendance->in_time)->format('H:i:s') : '-' }}</td>
                                                <td>{{ $attendance->out_time ? Carbon::parse($attendance->out_time)->format('H:i:s') : '-' }}</td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <tr><td colspan="5">No attendance found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
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
