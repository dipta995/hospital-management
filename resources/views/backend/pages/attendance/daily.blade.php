@extends('backend.layouts.master')
@section('title')
    Daily Attendance Sheet
@endsection
@push('styles')
    @include('backend.layouts.partials.report-styles')
    <style>
        .att-status { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 0.75rem; font-weight: 700; white-space: nowrap; }
        .att-status.present { background: #dcfce7; color: #166534; }
        .att-status.absence { background: #fee2e2; color: #b91c1c; }
        .att-status.leave { background: #e0f2fe; color: #075985; }
        .att-status.off_day { background: #f1f5f9; color: #475569; }
        .att-status.upcoming { background: #fef9c3; color: #854d0e; }
        .att-kpi-icon.present { background: #dcfce7; color: #16a34a; }
        .att-kpi-icon.absence { background: #fee2e2; color: #dc2626; }
        .att-kpi-icon.leave { background: #e0f2fe; color: #0284c7; }
        .att-kpi-icon.off_day { background: #f1f5f9; color: #475569; }
        .att-kpi-icon.open { background: #fef3c7; color: #d97706; }
        .att-kpi-icon.hours { background: #ede9fe; color: #7c3aed; }
        .att-kpi-icon.missing { background: #fee2e2; color: #b91c1c; }
        .att-kpi-icon.late { background: #ffedd5; color: #c2410c; }
        .att-kpi-icon.overtime { background: #dbeafe; color: #1d4ed8; }
        .att-flag { display: inline-block; padding: 1px 7px; border-radius: 6px; font-size: 0.72rem; font-weight: 700; margin: 1px 2px 1px 0; white-space: nowrap; }
        .att-flag.late { background: #ffedd5; color: #9a3412; }
        .att-flag.early { background: #fef9c3; color: #854d0e; }
        .att-flag.ot { background: #dbeafe; color: #1e40af; }
        .att-flag.missing { background: #fee2e2; color: #991b1b; }
        .att-flag.duty { background: #dcfce7; color: #166534; }
        .att-row-missing td { background: #fff5f5; }
        .att-session { font-size: 0.8rem; white-space: nowrap; }
        .att-row-absence td { background: #fff7f7; }
        .att-date-nav { display: flex; gap: 6px; align-items: flex-end; }
    </style>
@endpush
@php
    use App\Services\EmployeeAttendanceSummaryService as AttendanceSummary;

    $statusLabels = [
        'present' => 'Present',
        'absence' => 'Absent',
        'leave' => 'Leave',
        'off_day' => 'Weekly Off',
        'upcoming' => 'Upcoming',
    ];
    $totals = $sheet['totals'];
    $prevDate = $date->copy()->subDay()->toDateString();
    $nextDate = $date->copy()->addDay()->toDateString();
    $query = fn (array $extra = []) => array_filter(array_merge(['date' => $date->toDateString(), 'status' => $status], $extra));
@endphp
@section('admin-content')
    <div class="inv-page container-fluid py-3">
        @include('backend.layouts.partials.report-hero', [
            'reportTitle' => 'Daily Attendance Sheet',
            'reportSubtitle' => $date->format('d M Y') . ' · ' . $sheet['day_name'],
            'reportIcon' => 'fa-clipboard-list',
            'resetRoute' => route('admin.attendance.daily'),
        ])

        @include('backend.layouts.partials.message')
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="inv-panel mb-3">
            <form method="GET" action="{{ route('admin.attendance.daily') }}" class="inv-filter-toolbar">
                <div class="filter-field att-date-nav">
                    <a href="{{ route('admin.attendance.daily', $query(['date' => $prevDate])) }}" class="btn btn-outline-secondary" title="Previous day">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    <div>
                        <label class="form-label">Date</label>
                        <input type="date" class="form-control" name="date" value="{{ $date->toDateString() }}" onchange="this.form.submit()">
                    </div>
                    <a href="{{ route('admin.attendance.daily', $query(['date' => $nextDate])) }}" class="btn btn-outline-secondary" title="Next day">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
                <div class="filter-field">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        @foreach($statusLabels as $key => $label)
                            <option value="{{ $key }}" @selected($status === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="inv-filter-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Show</button>
                    <a href="{{ route('admin.attendance.daily', $query(['export' => 'pdf'])) }}" target="_blank" class="btn btn-danger">
                        <i class="fas fa-print"></i> Print / PDF
                    </a>
                    <a href="{{ route('admin.attendance.daily') }}" class="btn btn-outline-primary">Today</a>
                </div>
            </form>
        </div>

        <div class="inv-kpi-grid">
            <div class="inv-kpi">
                <div class="inv-kpi-icon collection"><i class="fas fa-users"></i></div>
                <div><div class="inv-kpi-label">Employees</div><div class="inv-kpi-value">{{ $totals['employees'] }}</div></div>
            </div>
            <div class="inv-kpi">
                <div class="inv-kpi-icon att-kpi-icon present"><i class="fas fa-user-check"></i></div>
                <div><div class="inv-kpi-label">Present</div><div class="inv-kpi-value">{{ $totals['present'] }}</div></div>
            </div>
            <div class="inv-kpi">
                <div class="inv-kpi-icon att-kpi-icon absence"><i class="fas fa-user-times"></i></div>
                <div><div class="inv-kpi-label">Absent</div><div class="inv-kpi-value">{{ $totals['absence'] }}</div></div>
            </div>
            <div class="inv-kpi">
                <div class="inv-kpi-icon att-kpi-icon leave"><i class="fas fa-umbrella-beach"></i></div>
                <div><div class="inv-kpi-label">Leave</div><div class="inv-kpi-value">{{ $totals['leave'] }}</div></div>
            </div>
            <div class="inv-kpi">
                <div class="inv-kpi-icon att-kpi-icon off_day"><i class="fas fa-bed"></i></div>
                <div><div class="inv-kpi-label">Weekly Off</div><div class="inv-kpi-value">{{ $totals['off_day'] }}</div></div>
            </div>
            <div class="inv-kpi">
                <div class="inv-kpi-icon att-kpi-icon open"><i class="fas fa-door-open"></i></div>
                <div><div class="inv-kpi-label">On Duty Now</div><div class="inv-kpi-value">{{ $totals['open'] }}</div></div>
            </div>
            <div class="inv-kpi">
                <div class="inv-kpi-icon att-kpi-icon missing"><i class="fas fa-exclamation-triangle"></i></div>
                <div><div class="inv-kpi-label">Missing OUT</div><div class="inv-kpi-value">{{ $totals['missing_out'] }}</div></div>
            </div>
            <div class="inv-kpi">
                <div class="inv-kpi-icon att-kpi-icon late"><i class="fas fa-user-clock"></i></div>
                <div><div class="inv-kpi-label">Late</div><div class="inv-kpi-value">{{ $totals['late'] }}</div></div>
            </div>
            <div class="inv-kpi">
                <div class="inv-kpi-icon att-kpi-icon overtime"><i class="fas fa-business-time"></i></div>
                <div><div class="inv-kpi-label">Overtime</div><div class="inv-kpi-value">{{ AttendanceSummary::formatMinutes($totals['overtime_minutes']) }}</div></div>
            </div>
            <div class="inv-kpi">
                <div class="inv-kpi-icon att-kpi-icon hours"><i class="fas fa-clock"></i></div>
                <div><div class="inv-kpi-label">Total Hours</div><div class="inv-kpi-value">{{ number_format($totals['hours'], 2) }}</div></div>
            </div>
        </div>

        <div class="inv-panel">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Status</th>
                        <th>First In</th>
                        <th>Last Out</th>
                        <th>Sessions</th>
                        <th>Worked</th>
                        <th>Expected</th>
                        <th>Short</th>
                        <th>Late / Early / OT</th>
                        <th>Note</th>
                        <th class="text-end">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($sheet['rows'] as $row)
                        @php $emp = $row['employee']; @endphp
                        <tr class="{{ $row['missing_out'] > 0 ? 'att-row-missing' : ($row['status'] === 'absence' ? 'att-row-absence' : '') }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-semibold">{{ $emp->name }}</div>
                                <small class="text-muted">{{ $emp->designation ?: '—' }}</small>
                                @if($row['shift'])
                                    <div><small class="text-primary">{{ $row['shift']->name }} · {{ $row['shift']->timeLabel() }}</small></div>
                                @endif
                            </td>
                            <td>
                                <span class="att-status {{ $row['status'] }}">{{ $statusLabels[$row['status']] ?? $row['status'] }}</span>
                                @if($row['leave_label'])
                                    <div><small class="text-muted">{{ $row['leave_label'] }}{{ $row['is_paid_leave'] === false ? ' (Unpaid)' : '' }}</small></div>
                                @endif
                            </td>
                            <td>{{ $row['first_in']?->format('h:i A') ?? '—' }}</td>
                            <td>
                                @if($row['missing_out'] > 0)
                                    <span class="att-flag missing">Missing OUT</span>
                                @elseif($row['open_sessions'] > 0)
                                    <span class="att-flag duty">On duty</span>
                                @else
                                    {{ $row['last_out']?->format('h:i A') ?? '—' }}
                                    @if($row['last_out'] && $row['last_out']->toDateString() !== $date->toDateString())
                                        <small class="text-primary fw-semibold">(+1)</small>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @forelse($row['sessions'] as $session)
                                    @php
                                        $sessionOut = $session->out_time ? \Carbon\Carbon::parse($session->out_time) : null;
                                    @endphp
                                    <div class="att-session">
                                        {{ $session->in_time ? \Carbon\Carbon::parse($session->in_time)->format('h:i A') : '—' }}
                                        →
                                        @if($sessionOut)
                                            {{ $sessionOut->format('h:i A') }}
                                            @if($sessionOut->toDateString() !== $date->toDateString())
                                                <small class="text-primary fw-semibold">(+1)</small>
                                            @endif
                                        @else
                                            Open
                                        @endif
                                    </div>
                                @empty
                                    <span class="text-muted">—</span>
                                @endforelse
                            </td>
                            <td class="fw-semibold">{{ $row['worked_minutes'] ? AttendanceSummary::formatMinutes($row['worked_minutes']) : '—' }}</td>
                            <td>{{ in_array($row['status'], ['present', 'absence'], true) ? rtrim(rtrim(number_format($row['expected_hours'], 2), '0'), '.') . 'h' : '—' }}</td>
                            <td class="{{ $row['short_minutes'] > 0 ? 'text-danger fw-semibold' : 'text-muted' }}">
                                {{ $row['short_minutes'] > 0 ? AttendanceSummary::formatMinutes($row['short_minutes']) : '—' }}
                            </td>
                            <td>
                                @if($row['late_minutes'] > 0)
                                    <span class="att-flag late">Late {{ AttendanceSummary::formatMinutes($row['late_minutes']) }}</span>
                                @endif
                                @if($row['early_minutes'] > 0)
                                    <span class="att-flag early">Early {{ AttendanceSummary::formatMinutes($row['early_minutes']) }}</span>
                                @endif
                                @if($row['overtime_minutes'] > 0)
                                    <span class="att-flag ot">OT {{ AttendanceSummary::formatMinutes($row['overtime_minutes']) }}</span>
                                @endif
                                @if(!$row['late_minutes'] && !$row['early_minutes'] && !$row['overtime_minutes'])
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td><small>{{ $row['notes'] ?: '' }}</small></td>
                            <td class="text-end">
                                @if($row['status'] !== 'upcoming')
                                    <button type="button" class="btn btn-sm btn-outline-success att-add-btn"
                                            data-employee="{{ $emp->id }}" data-name="{{ $emp->name }}"
                                            data-bs-toggle="modal" data-bs-target="#dailyAddAttendanceModal">
                                        <i class="fas fa-plus"></i> Add
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="11" class="text-center text-muted py-4">No employee found for this filter.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="dailyAddAttendanceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.attendance.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Add Attendance — <span id="dailyAddEmployeeName"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="employee_id" id="dailyAddEmployeeId">
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="date" class="form-control" value="{{ $date->toDateString() }}" required>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label">In Time</label>
                                <input type="time" name="in_time" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Out Time <small class="text-muted">(optional)</small></label>
                                <input type="time" name="out_time" class="form-control">
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Note <small class="text-muted">(optional)</small></label>
                            <textarea name="note" class="form-control" rows="2" maxlength="500"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.att-add-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.getElementById('dailyAddEmployeeId').value = btn.dataset.employee;
                document.getElementById('dailyAddEmployeeName').textContent = btn.dataset.name;
            });
        });
    </script>
    @include('backend.pages.attendance.partials.overnight-hint')
@endpush
