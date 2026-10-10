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
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        <div class="mb-3">
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addAttendanceModal">
                                + Add Attendance
                            </button>
                            <a href="{{ route('admin.attendance.daily') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-clipboard-list"></i> Daily Sheet
                            </a>
                            <a href="{{ route('admin.attendance-shifts.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-business-time"></i> Shifts
                            </a>
                            <a href="{{ route('admin.attendance.repair', ['month' => $month, 'year' => $year]) }}" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-tools"></i> Fix Night Duty Punches
                            </a>
                        </div>
                        @if($errors->any())
                            <div class="alert alert-danger">{{ $errors->first() }}</div>
                        @endif
                        <form method="get" class="row mb-3">
                            <div class="col-md-3">
                                <label for="employee_id" class="form-label">Employee</label>
                                <select class="form-select" name="employee_id" id="employee_id">
                                    <option value="">All Employees</option>
                                    @foreach($employees ?? [] as $emp)
                                        <option value="{{ $emp->id }}" {{ (string)$employeeId === (string)$emp->id ? 'selected' : '' }}>
                                            {{ $emp->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
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
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-info">Submit</button>
                            </div>
                        </form>

                        @if(!empty($canSummarizeAttendance) && !empty($employeeSummaries))
                            <div class="table-responsive mb-4">
                                <h5 class="mb-3">Attendance Summary — {{ $month }} {{ $year }}</h5>
                                <table class="table table-sm table-striped table-bordered">
                                    <thead class="table-dark">
                                    <tr>
                                        <th>Employee</th>
                                        <th>Expected</th>
                                        <th>Present</th>
                                        <th>Off Days</th>
                                        <th>Leave</th>
                                        <th>Absent</th>
                                        <th>Hours</th>
                                        <th>Rate</th>
                                        <th>Late</th>
                                        <th>Early</th>
                                        <th>OT</th>
                                        <th>Missing OUT</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($employeeSummaries as $empId => $summary)
                                        @php $emp = $employees->firstWhere('id', $empId); @endphp
                                        <tr>
                                            <td><strong>{{ $emp->name ?? 'N/A' }}</strong></td>
                                            <td>{{ $summary['expectedWorkingDays'] }}</td>
                                            <td class="text-success">{{ $summary['presentCount'] }}</td>
                                            <td>{{ $summary['weeklyOffCount'] }}</td>
                                            <td class="text-info">{{ $summary['leaveCount'] }}</td>
                                            <td class="text-danger">{{ $summary['absenceCount'] }}</td>
                                            <td>{{ number_format($summary['totalHours'], 2) }}</td>
                                            <td>{{ $summary['attendanceRate'] }}%</td>
                                            <td class="{{ $summary['lateCount'] ? 'text-warning fw-semibold' : '' }}">
                                                {{ $summary['lateCount'] }}
                                                @if($summary['lateMinutes'])<small class="text-muted">({{ \App\Services\EmployeeAttendanceSummaryService::formatMinutes($summary['lateMinutes']) }})</small>@endif
                                            </td>
                                            <td>{{ $summary['earlyLeaveCount'] }}</td>
                                            <td class="text-primary">{{ $summary['overtimeMinutes'] ? \App\Services\EmployeeAttendanceSummaryService::formatMinutes($summary['overtimeMinutes']) : '0' }}</td>
                                            <td class="{{ $summary['missingOutCount'] ? 'text-danger fw-bold' : '' }}">{{ $summary['missingOutCount'] }}</td>
                                            <td>
                                                <a href="{{ route('admin.employees.leave-days.index', ['employee' => $empId, 'month' => $month, 'year' => $year]) }}"
                                                   class="btn btn-sm btn-outline-primary">Manage</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

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
                                    @php
                                        use Carbon\Carbon;
                                        $missingOutBefore = Carbon::now('Asia/Dhaka')->subHours(
                                            \App\Services\AttendanceSettings::forBranch(auth()->user()->branch_id)->maxSessionHours()
                                        );
                                    @endphp
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
                                                            | OUT:
                                                            @if($session->out_time)
                                                                {{ Carbon::parse($session->out_time)->format('h:i:s A') }}
                                                                @if(Carbon::parse($session->out_time)->toDateString() !== Carbon::parse($session->date)->toDateString())
                                                                    <span class="text-primary fw-semibold">(+1 day)</span>
                                                                @endif
                                                            @elseif(Carbon::parse($session->in_time, 'Asia/Dhaka')->lt($missingOutBefore))
                                                                <span class="badge bg-danger">Missing OUT</span>
                                                            @else
                                                                <span class="badge bg-success">On duty</span>
                                                            @endif
                                                            | Mode: {{ ucfirst($session->mode ?? 'standard') }}
                                                            @if($session->note) | Note: {{ $session->note }} @endif
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
                                                                <label class="form-label">Note</label>
                                                                <textarea name="note" class="form-control" rows="1" maxlength="500">{{ old('note', $session->note) }}</textarea>
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
    @include('backend.pages.attendance.partials.overnight-hint')
@endpush

<!-- Add Attendance Modal -->
<div class="modal fade" id="addAttendanceModal" tabindex="-1" aria-labelledby="addAttendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.attendance.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addAttendanceModalLabel">Add Attendance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Employee</label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">-- Select Employee --</option>
                            @foreach($employees ?? [] as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">In Time</label>
                        <input type="time" name="in_time" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Out Time <small class="text-muted">(optional)</small></label>
                        <input type="time" name="out_time" class="form-control">
                    </div>
                    <div class="mb-3">
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
