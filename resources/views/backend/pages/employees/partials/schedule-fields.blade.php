@if(!empty($hrSchemaInstalled))
    <div class="card mt-3 border-info">
        <div class="card-body">
            <h5 class="card-title text-info">
                <i class="fas fa-calendar-week"></i> Work Schedule & Leave Settings
            </h5>
            <p class="text-muted small mb-3">
                Set recurring weekly off days and working hours. These are used in attendance and salary calculations.
            </p>

            <div class="form-group mb-3">
                <label class="form-label d-block">Weekly Off Days</label>
                <div class="d-flex flex-wrap gap-3">
                    @php
                        $employeeRecord = $edited ?? null;
                        $selectedOffDays = old('weekly_off_days', $employeeRecord->weekly_off_days ?? []);
                        if (is_string($selectedOffDays)) {
                            $selectedOffDays = json_decode($selectedOffDays, true) ?? [];
                        }
                    @endphp
                    @foreach($weekDays as $day)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   name="weekly_off_days[]"
                                   value="{{ $day }}"
                                   id="off_day_{{ $day }}"
                                   {{ in_array($day, $selectedOffDays ?? [], true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="off_day_{{ $day }}">{{ $day }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="working_hours_per_day">Working Hours Per Day</label>
                        <input type="number" step="0.5" min="1" max="24"
                               class="form-control" id="working_hours_per_day"
                               name="working_hours_per_day"
                               value="{{ old('working_hours_per_day', $employeeRecord->working_hours_per_day ?? 8) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="annual_leave_quota">Annual Leave Quota (Days)</label>
                        <input type="number" min="0" max="365"
                               class="form-control" id="annual_leave_quota"
                               name="annual_leave_quota"
                               value="{{ old('annual_leave_quota', $employeeRecord->annual_leave_quota ?? 12) }}">
                    </div>
                </div>
            </div>

            @if(!empty($employeeRecord?->id))
                <div class="mt-3">
                    <a href="{{ route('admin.employees.leave-days.index', $employeeRecord->id) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-calendar-alt"></i> Manage Leave Days
                    </a>
                </div>
            @endif
        </div>
    </div>
@endif
