@extends('backend.layouts.master')
@section('title')
    Duty Shifts
@endsection
@push('styles')
    <style>
        .shift-time { font-weight: 600; white-space: nowrap; }
        .shift-inactive td { color: #94a3b8; }
    </style>
@endpush
@section('admin-content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">
                                Duty Shifts
                                @if($installed)
                                    <button type="button" class="btn btn-success btn-sm float-end shift-open-btn"
                                            data-bs-toggle="modal" data-bs-target="#shiftModal"
                                            data-action="{{ route('admin.attendance-shifts.store') }}" data-method="POST">
                                        <i class="fas fa-plus"></i> New Shift
                                    </button>
                                @endif
                                <a href="{{ route('admin.attendance.daily') }}" class="btn btn-outline-primary btn-sm float-end me-2">Daily Sheet</a>
                            </h4>

                            @include('backend.layouts.partials.message')
                            @if($errors->any())
                                <div class="alert alert-danger">{{ $errors->first() }}</div>
                            @endif

                            @if(!$installed)
                                <div class="alert alert-warning">
                                    Shifts are not installed yet. Install <strong>Attendance Shifts</strong> from Dashboard → System Updates.
                                </div>
                            @else
                                <p class="text-muted small mb-3">
                                    Assign a shift to each employee from the employee edit page. Late, early leave and overtime are
                                    calculated against the shift. If the end time is earlier than the start time (e.g. 10:00 PM – 8:00 AM),
                                    the shift ends on the next day and the whole duty is counted on the start date.
                                </p>

                                <div class="table-responsive">
                                    <table class="table table-striped align-middle">
                                        <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Time</th>
                                            <th>Break</th>
                                            <th>Paid Hours</th>
                                            <th>Employees</th>
                                            <th>Status</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($shifts as $shift)
                                            <tr class="{{ $shift->is_active ? '' : 'shift-inactive' }}">
                                                <td class="fw-semibold">{{ $shift->name }}</td>
                                                <td class="shift-time">
                                                    {{ $shift->timeLabel() }}
                                                    @if($shift->isOvernight())
                                                        <span class="badge bg-dark ms-1">Night</span>
                                                    @endif
                                                </td>
                                                <td>{{ $shift->break_minutes ? $shift->break_minutes . ' min' : '—' }}</td>
                                                <td>{{ \App\Services\EmployeeAttendanceSummaryService::formatMinutes($shift->expectedMinutes()) }}</td>
                                                <td>{{ $shift->employees_count }}</td>
                                                <td>
                                                    <span class="badge {{ $shift->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ $shift->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-sm btn-outline-primary shift-open-btn"
                                                            data-bs-toggle="modal" data-bs-target="#shiftModal"
                                                            data-action="{{ route('admin.attendance-shifts.update', $shift->id) }}" data-method="PUT"
                                                            data-name="{{ $shift->name }}"
                                                            data-type="{{ $shift->is_flexible ? 'flexible' : 'fixed' }}"
                                                            data-start="{{ $shift->start_time ? substr($shift->start_time, 0, 5) : '' }}"
                                                            data-end="{{ $shift->end_time ? substr($shift->end_time, 0, 5) : '' }}"
                                                            data-break="{{ $shift->break_minutes }}"
                                                            data-hours="{{ $shift->flexible_hours }}"
                                                            data-active="{{ $shift->is_active ? 1 : 0 }}">
                                                        <i class="fas fa-pencil"></i>
                                                    </button>
                                                    <form action="{{ route('admin.attendance-shifts.destroy', $shift->id) }}" method="POST" class="d-inline"
                                                          onsubmit="return confirm('Delete this shift? If it is in use it will be deactivated instead.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">
                                                    No shift yet. Create shifts like Morning (8 AM – 4 PM), Evening (4 PM – 12 AM), Night (12 AM – 8 AM).
                                                </td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($installed)
        <div class="modal fade" id="shiftModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.attendance-shifts.store') }}" class="modal-content" id="shiftForm">
                    @csrf
                    <input type="hidden" name="_method" value="POST" id="shiftMethod">
                    <div class="modal-header">
                        <h5 class="modal-title" id="shiftModalTitle">New Shift</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label" for="shift_name">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="shift_name" maxlength="100" required placeholder="e.g. Night">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="shift_type">Type</label>
                            <select class="form-select" name="type" id="shift_type">
                                <option value="fixed">Fixed time (late / early leave tracked)</option>
                                <option value="flexible">Flexible (only total hours, e.g. doctors on call)</option>
                            </select>
                        </div>
                        <div class="row g-2 mb-3 shift-fixed">
                            <div class="col-6">
                                <label class="form-label" for="shift_start">Start</label>
                                <input type="time" class="form-control" name="start_time" id="shift_start">
                            </div>
                            <div class="col-6">
                                <label class="form-label" for="shift_end">End</label>
                                <input type="time" class="form-control" name="end_time" id="shift_end">
                            </div>
                            <div class="col-12">
                                <small class="fw-semibold text-primary" id="shiftOvernightHint"></small>
                            </div>
                        </div>
                        <div class="mb-3 shift-fixed">
                            <label class="form-label" for="shift_break">Unpaid break (minutes)</label>
                            <input type="number" class="form-control" name="break_minutes" id="shift_break" min="0" max="240" value="0">
                            <small class="text-muted">Deducted once from a day's worked time if more than 4 hours were punched.</small>
                        </div>
                        <div class="mb-3 shift-flexible d-none">
                            <label class="form-label" for="shift_hours">Required hours per duty</label>
                            <input type="number" class="form-control" name="flexible_hours" id="shift_hours" min="1" max="24" step="0.5">
                        </div>
                        <div class="form-check">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="shift_active" checked>
                            <label class="form-check-label" for="shift_active">Active</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        (function () {
            var modal = document.getElementById('shiftModal');
            if (!modal) {
                return;
            }
            var form = document.getElementById('shiftForm');
            var type = document.getElementById('shift_type');
            var start = document.getElementById('shift_start');
            var end = document.getElementById('shift_end');

            function toggleType() {
                var flexible = type.value === 'flexible';
                form.querySelectorAll('.shift-fixed').forEach(function (el) { el.classList.toggle('d-none', flexible); });
                form.querySelectorAll('.shift-flexible').forEach(function (el) { el.classList.toggle('d-none', !flexible); });
                start.required = end.required = !flexible;
                document.getElementById('shift_hours').required = flexible;
            }

            function overnightHint() {
                document.getElementById('shiftOvernightHint').textContent =
                    start.value && end.value && end.value <= start.value ? 'Night shift: ends on the next day (+1).' : '';
            }

            type.addEventListener('change', toggleType);
            start.addEventListener('input', overnightHint);
            end.addEventListener('input', overnightHint);

            modal.addEventListener('show.bs.modal', function (event) {
                var d = event.relatedTarget.dataset;
                var editing = d.method === 'PUT';
                form.action = d.action;
                document.getElementById('shiftMethod').value = d.method;
                document.getElementById('shiftModalTitle').textContent = editing ? 'Edit Shift' : 'New Shift';
                document.getElementById('shift_name').value = d.name || '';
                type.value = d.type || 'fixed';
                start.value = d.start || '';
                end.value = d.end || '';
                document.getElementById('shift_break').value = d.break || 0;
                document.getElementById('shift_hours').value = d.hours || '';
                document.getElementById('shift_active').checked = !editing || d.active === '1';
                toggleType();
                overnightHint();
            });
        })();
    </script>
@endpush
