@extends('backend.layouts.master')
@section('title')
    Leave & Off Days - {{ $employee->name }}
@endsection
@push('styles')
    <style>
        .stat-card { border-radius: 8px; padding: 16px; background: #f8f9fa; height: 100%; }
        .stat-value { font-size: 1.5rem; font-weight: 700; }
        .status-present { color: #27ae60; }
        .status-off_day { color: #6c757d; }
        .status-leave { color: #2980b9; }
        .status-absence { color: #e74c3c; }
        .calendar-day { font-size: 12px; padding: 6px; border: 1px solid #eee; min-height: 52px; }
        .calendar-day .badge { font-size: 10px; }
    </style>
@endpush
@section('admin-content')
    <div class="main-panel">
        <div class="content-wrapper">
            @include('backend.layouts.partials.message')

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-1">{{ $employee->name }} — Leave & Off Days</h4>
                    <p class="text-muted mb-0">{{ $employee->designation ?? 'Employee' }} | {{ $month }} {{ $year }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.employees.edit', $employee->id) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Employee
                    </a>
                    <a href="{{ route('admin.employees.salary-sheet', ['month' => $month, 'year' => $year]) }}" class="btn btn-outline-info btn-sm">
                        Salary Sheet
                    </a>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <form method="get" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Month</label>
                            <select class="form-select" name="month">
                                @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)
                                    <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Year</label>
                            <select class="form-select" name="year">
                                @for($y = date('Y')-2; $y <= date('Y')+1; $y++)
                                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-info">Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-2"><div class="stat-card"><div class="text-muted small">Expected Work Days</div><div class="stat-value">{{ $summary['expectedWorkingDays'] }}</div></div></div>
                <div class="col-md-2"><div class="stat-card"><div class="text-muted small">Present</div><div class="stat-value status-present">{{ $summary['presentCount'] }}</div></div></div>
                <div class="col-md-2"><div class="stat-card"><div class="text-muted small">Weekly Off</div><div class="stat-value status-off_day">{{ $summary['weeklyOffCount'] }}</div></div></div>
                <div class="col-md-2"><div class="stat-card"><div class="text-muted small">Leave Days</div><div class="stat-value status-leave">{{ $summary['leaveCount'] }}</div></div></div>
                <div class="col-md-2"><div class="stat-card"><div class="text-muted small">Absences</div><div class="stat-value status-absence">{{ $summary['absenceCount'] }}</div></div></div>
                <div class="col-md-2"><div class="stat-card"><div class="text-muted small">Attendance Rate</div><div class="stat-value">{{ $summary['attendanceRate'] }}%</div></div></div>
            </div>

            <div class="row">
                <div class="col-lg-5">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Weekly Schedule</h5>
                            <form method="post" action="{{ route('admin.employees.schedule.update', $employee->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    @php $selectedOff = $employee->weekly_off_days ?? []; @endphp
                                    @foreach($weekDays as $day)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="weekly_off_days[]"
                                                   value="{{ $day }}" id="schedule_{{ $day }}"
                                                   {{ in_array($day, $selectedOff, true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="schedule_{{ $day }}">{{ $day }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <label class="form-label">Hours/Day</label>
                                        <input type="number" step="0.5" class="form-control" name="working_hours_per_day"
                                               value="{{ $employee->working_hours_per_day ?? 8 }}">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Annual Quota</label>
                                        <input type="number" class="form-control" name="annual_leave_quota"
                                               value="{{ $employee->annual_leave_quota ?? 12 }}">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success btn-sm">Save Schedule</button>
                            </form>
                            <p class="text-muted small mt-2 mb-0">
                                Leave used this year: <strong>{{ $summary['leaveUsedYtd'] }}</strong> /
                                <strong>{{ $summary['annualLeaveQuota'] }}</strong>
                                (Remaining: {{ $summary['remainingLeaveQuota'] }})
                            </p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Add Leave Day</h5>
                            <form method="post" action="{{ route('admin.employees.leave-days.store', $employee->id) }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Date</label>
                                    <input type="date" name="date" class="form-control" required
                                           min="{{ \Carbon\Carbon::createFromFormat('F Y', "$month $year")->startOfMonth()->format('Y-m-d') }}"
                                           max="{{ \Carbon\Carbon::createFromFormat('F Y', "$month $year")->endOfMonth()->format('Y-m-d') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Leave Type</label>
                                    <select name="type" class="form-select" required>
                                        @foreach($leaveTypes as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Reason</label>
                                    <textarea name="reason" class="form-control" rows="2" maxlength="500"></textarea>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="is_paid" value="1" id="is_paid" checked>
                                    <label class="form-check-label" for="is_paid">Paid Leave</label>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">Add Leave</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Monthly Calendar</h5>
                            <div class="row g-1">
                                @foreach($summary['dailyBreakdown'] as $day)
                                    <div class="col-2 col-md-1">
                                        <div class="calendar-day text-center">
                                            <div><strong>{{ \Carbon\Carbon::parse($day['date'])->format('d') }}</strong></div>
                                            <span class="badge bg-light text-dark">{{ substr($day['day_name'], 0, 3) }}</span>
                                            <div class="status-{{ $day['status'] }} small">
                                                @if($day['status'] === 'off_day') Off
                                                @elseif($day['status'] === 'leave') Leave
                                                @elseif($day['status'] === 'present') Present
                                                @else Absent
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body table-responsive">
                            <h5 class="card-title">Leave Records — {{ $month }} {{ $year }}</h5>
                            <table class="table table-sm table-bordered">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Paid</th>
                                    <th>Reason</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($leaveDays as $leave)
                                    <tr>
                                        <td>{{ $leave->date->format('d M Y') }}</td>
                                        <td>{{ $leave->type_label }}</td>
                                        <td>{{ $leave->is_paid ? 'Yes' : 'No' }}</td>
                                        <td>{{ $leave->reason ?? '-' }}</td>
                                        <td>
                                            <form method="post" action="{{ route('admin.employees.leave-days.destroy', [$employee->id, $leave->id]) }}"
                                                  onsubmit="return confirm('Remove this leave day?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted">No leave records for this month.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
