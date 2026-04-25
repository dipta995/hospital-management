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
                                        <th>Employee</th>
                                        <th>Total Sessions</th>
                                        <th>Total Work Time</th>
                                        <th>Session Details (IN/OUT)</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php use Carbon\Carbon; @endphp
                                    @forelse($groupedAttendances ?? [] as $date => $items)
                                        @php
                                            $employeeGroups = $items->groupBy('employee_id');
                                        @endphp
                                        <tr class="table-secondary">
                                            <td><strong>{{ Carbon::parse($date)->format('d M Y') }}</strong></td>
                                            <td><strong>{{ $items->pluck('employee_id')->unique()->count() }} Employees</strong></td>
                                            <td colspan="4"></td>
                                        </tr>
                                        @foreach($employeeGroups as $employeeAttendances)
                                            @php
                                                $sorted = $employeeAttendances->sortBy('in_time')->values();
                                                $totalSeconds = 0;
                                                foreach ($sorted as $row) {
                                                    if ($row->in_time && $row->out_time) {
                                                        $totalSeconds += Carbon::parse($row->out_time)->diffInSeconds(Carbon::parse($row->in_time));
                                                    }
                                                }
                                                $totalDuration = sprintf('%02d:%02d', floor($totalSeconds / 3600), floor(($totalSeconds % 3600) / 60));
                                            @endphp
                                            <tr>
                                                <td></td>
                                                <td>{{ optional($sorted->first()->employee)->name ?? '-' }}</td>
                                                <td>{{ $sorted->count() }}</td>
                                                <td>{{ $totalDuration }} (HH:MM)</td>
                                                <td>
                                                    @foreach($sorted as $session)
                                                        <div>
                                                            {{ $loop->iteration }}. IN: {{ $session->in_time ? Carbon::parse($session->in_time)->format('h:i:s A') : '-' }}
                                                            | OUT: {{ $session->out_time ? Carbon::parse($session->out_time)->format('h:i:s A') : 'Open' }}
                                                            | Mode: {{ ucfirst($session->mode ?? 'standard') }}
                                                        </div>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @foreach($sorted as $session)
                                                        <button type="button" class="btn btn-sm btn-primary mb-1"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#edit-attendance-{{ $session->id }}"
                                                                aria-expanded="false">
                                                            Edit #{{ $loop->iteration }}
                                                        </button>
                                                    @endforeach
                                                </td>
                                            </tr>
                                            @foreach($sorted as $session)
                                                <tr class="collapse" id="edit-attendance-{{ $session->id }}">
                                                    <td colspan="6">
                                                        <form method="post" action="{{ route('admin.attendance.update-time', $session->id) }}" class="row g-2 align-items-end">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="col-md-3">
                                                                <label class="form-label">Date</label>
                                                                <input type="date" name="date" class="form-control"
                                                                       value="{{ old('date', Carbon::parse($session->date)->format('Y-m-d')) }}" required>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <label class="form-label">In Time</label>
                                                                <input type="time" name="in_time" class="form-control"
                                                                       value="{{ old('in_time', Carbon::parse($session->in_time)->format('H:i')) }}" required>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <label class="form-label">Out Time</label>
                                                                <input type="time" name="out_time" class="form-control"
                                                                       value="{{ old('out_time', $session->out_time ? Carbon::parse($session->out_time)->format('H:i') : '') }}">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <small class="text-muted">Edit this specific IN/OUT pair.</small>
                                                            </div>
                                                            <div class="col-md-2 text-end">
                                                                <button type="submit" class="btn btn-success btn-sm">Save</button>
                                                            </div>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    @empty
                                        <tr><td colspan="6">No attendance found.</td></tr>
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
