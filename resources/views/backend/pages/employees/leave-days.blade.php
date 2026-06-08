@extends('backend.layouts.master')
@section('title')
    Leave & Off Days - {{ $employee->name }}
@endsection
@push('styles')
    <style>
        .stat-card {
            border-radius: 10px;
            padding: 14px 16px;
            background: #fff;
            height: 100%;
            border: 1px solid #e2e8f0;
            border-left-width: 4px;
        }
        .stat-card.stat-present { border-left-color: #16a34a; }
        .stat-card.stat-off { border-left-color: #64748b; }
        .stat-card.stat-leave { border-left-color: #2563eb; }
        .stat-card.stat-absence { border-left-color: #dc2626; }
        .stat-card.stat-expected { border-left-color: #0f766e; }
        .stat-card.stat-rate { border-left-color: #7c3aed; }
        .stat-value { font-size: 1.45rem; font-weight: 700; }
        .stat-present .stat-value { color: #16a34a; }
        .stat-off .stat-value { color: #475569; }
        .stat-leave .stat-value { color: #2563eb; }
        .stat-absence .stat-value { color: #dc2626; }
        .stat-expected .stat-value { color: #0f766e; }
        .stat-rate .stat-value { color: #7c3aed; }

        .calendar-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 14px;
        }
        .legend-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.82rem;
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 999px;
            border: 1px solid transparent;
        }
        .legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 4px;
            flex-shrink: 0;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 6px;
        }
        .calendar-weekday {
            text-align: center;
            font-size: 0.75rem;
            font-weight: 700;
            color: #64748b;
            padding: 6px 0;
            text-transform: uppercase;
        }
        .calendar-day {
            font-size: 11px;
            padding: 8px 4px;
            border-radius: 10px;
            min-height: 68px;
            text-align: center;
            border: 2px solid transparent;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            cursor: default;
        }
        .calendar-day:hover,
        .calendar-day:focus-within {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.12);
            outline: none;
        }
        .calendar-day-empty {
            min-height: 68px;
            visibility: hidden;
        }
        .calendar-day .day-num {
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.1;
        }
        .calendar-day .day-label {
            display: inline-block;
            margin-top: 4px;
            padding: 2px 6px;
            border-radius: 999px;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .calendar-day.status-present {
            background: #dcfce7;
            border-color: #86efac;
            color: #14532d;
        }
        .calendar-day.status-present .day-label {
            background: #16a34a;
            color: #fff;
        }

        .calendar-day.status-absence {
            background: #fee2e2;
            border-color: #fca5a5;
            color: #7f1d1d;
        }
        .calendar-day.status-absence .day-label {
            background: #dc2626;
            color: #fff;
        }

        .calendar-day.status-leave {
            background: #dbeafe;
            border-color: #93c5fd;
            color: #1e3a8a;
        }
        .calendar-day.status-leave .day-label {
            background: #2563eb;
            color: #fff;
        }

        .calendar-day.status-off_day {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: #334155;
        }
        .calendar-day.status-off_day .day-label {
            background: #64748b;
            color: #fff;
        }

        .calendar-day.status-upcoming {
            background: #f8fafc;
            border-color: #e2e8f0;
            border-style: dashed;
            color: #94a3b8;
        }
        .calendar-day.status-upcoming .day-label {
            background: #e2e8f0;
            color: #64748b;
        }

        .legend-present { background: #dcfce7; border-color: #86efac; color: #14532d; }
        .legend-present .legend-dot { background: #16a34a; }
        .legend-absence { background: #fee2e2; border-color: #fca5a5; color: #7f1d1d; }
        .legend-absence .legend-dot { background: #dc2626; }
        .legend-leave { background: #dbeafe; border-color: #93c5fd; color: #1e3a8a; }
        .legend-leave .legend-dot { background: #2563eb; }
        .legend-off { background: #f1f5f9; border-color: #cbd5e1; color: #334155; }
        .legend-off .legend-dot { background: #64748b; }
        .legend-upcoming { background: #f8fafc; border-color: #e2e8f0; color: #64748b; }
        .legend-upcoming .legend-dot { background: #cbd5e1; }
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

            <div class="row mb-4 g-3">
                <div class="col-md-2"><div class="stat-card stat-expected"><div class="text-muted small">Expected Work Days</div><div class="stat-value">{{ $summary['expectedWorkingDays'] }}</div></div></div>
                <div class="col-md-2"><div class="stat-card stat-present"><div class="text-muted small">Present</div><div class="stat-value">{{ $summary['presentCount'] }}</div></div></div>
                <div class="col-md-2"><div class="stat-card stat-off"><div class="text-muted small">Weekly Off</div><div class="stat-value">{{ $summary['weeklyOffCount'] }}</div></div></div>
                <div class="col-md-2"><div class="stat-card stat-leave"><div class="text-muted small">Leave Days</div><div class="stat-value">{{ $summary['leaveCount'] }}</div></div></div>
                <div class="col-md-2"><div class="stat-card stat-absence"><div class="text-muted small">Absences</div><div class="stat-value">{{ $summary['absenceCount'] }}</div></div></div>
                <div class="col-md-2"><div class="stat-card stat-rate"><div class="text-muted small">Attendance Rate</div><div class="stat-value">{{ $summary['attendanceRate'] }}%</div></div></div>
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

                            <div class="calendar-legend">
                                <span class="legend-item legend-present"><span class="legend-dot"></span> Present</span>
                                <span class="legend-item legend-absence"><span class="legend-dot"></span> Absent</span>
                                <span class="legend-item legend-leave"><span class="legend-dot"></span> Leave</span>
                                <span class="legend-item legend-off"><span class="legend-dot"></span> Weekly Off</span>
                                <span class="legend-item legend-upcoming"><span class="legend-dot"></span> Upcoming</span>
                            </div>

                            @php
                                $monthStart = \Carbon\Carbon::createFromFormat('F Y', "$month $year")->startOfMonth();
                                $leadingEmpty = $monthStart->dayOfWeek;
                                $statusLabels = [
                                    'present' => 'Present',
                                    'absence' => 'Absent',
                                    'leave' => 'Leave',
                                    'off_day' => 'Off',
                                    'upcoming' => 'Soon',
                                ];
                            @endphp

                            <div class="calendar-grid">
                                @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $weekday)
                                    <div class="calendar-weekday">{{ $weekday }}</div>
                                @endforeach

                                @for($i = 0; $i < $leadingEmpty; $i++)
                                    <div class="calendar-day-empty"></div>
                                @endfor

                                @foreach($summary['dailyBreakdown'] as $day)
                                    <div class="calendar-day status-{{ $day['status'] }}"
                                         tabindex="0"
                                         title="{{ \Carbon\Carbon::parse($day['date'])->format('d M Y') }} — {{ $statusLabels[$day['status']] ?? ucfirst($day['status']) }}">
                                        <div class="day-num">{{ \Carbon\Carbon::parse($day['date'])->format('d') }}</div>
                                        <span class="day-label">{{ $statusLabels[$day['status']] ?? ucfirst($day['status']) }}</span>
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
