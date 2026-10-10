@extends('backend.layouts.master')
@section('title')
    Fix Night Duty Punches
@endsection
@push('styles')
    <style>
        .repair-card { border: 1px solid #e2e8f0; border-radius: 10px; margin-bottom: 14px; }
        .repair-card-head { display: flex; align-items: center; gap: 10px; padding: 10px 14px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; border-radius: 10px 10px 0 0; }
        .repair-cols { display: grid; grid-template-columns: 1fr 1fr; gap: 0; }
        .repair-cols > div { padding: 10px 14px; }
        .repair-cols > div + div { border-left: 1px solid #e2e8f0; }
        .repair-cols table { font-size: 0.8rem; margin: 0; }
        .repair-long { color: #b45309; font-weight: 700; }
        @media (max-width: 768px) { .repair-cols { grid-template-columns: 1fr; } .repair-cols > div + div { border-left: 0; border-top: 1px solid #e2e8f0; } }
    </style>
@endpush
@php
    use App\Services\EmployeeAttendanceSummaryService as AttendanceSummary;

    $fmt = function ($row) {
        $in = $row['in'];
        $out = $row['out'];
        return [
            'date' => \Carbon\Carbon::parse($row['date'])->format('d M'),
            'in' => $in->format('d M h:i A'),
            'out' => $out ? $out->format(($out->toDateString() === $in->toDateString() ? '' : 'd M ') . 'h:i A') : null,
            'minutes' => $out ? $in->diffInMinutes($out) : null,
        ];
    };
@endphp
@section('admin-content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        Fix Night Duty Punches — {{ $month }} {{ $year }}
                        <a href="{{ route('admin.attendance.index', ['month' => $month, 'year' => $year]) }}" class="btn btn-outline-secondary btn-sm float-end">Back</a>
                    </h4>
                    @include('backend.layouts.partials.message')
                    @if($errors->any())
                        <div class="alert alert-danger">{{ $errors->first() }}</div>
                    @endif

                    <div class="alert alert-info small">
                        Before this update, a punch after midnight (e.g. OUT at 2 AM) was saved as a new IN on the next day,
                        so the night duty stayed open and later punches got mixed up. This tool replays the card punches
                        of the month with the new rules and shows the result. <strong>Nothing changes until you press Apply.</strong>
                        Manually added entries with a note are not touched.
                    </div>

                    <form method="GET" class="row g-2 mb-3">
                        <div class="col-md-3">
                            <select name="month" class="form-select">
                                @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $m)
                                    <option value="{{ $m }}" @selected($m === $month)>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="year" class="form-select">
                                @for($y = date('Y') - 2; $y <= date('Y'); $y++)
                                    <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary" type="submit">Check</button>
                        </div>
                    </form>

                    @if(empty($plans))
                        <div class="alert alert-success mb-0">
                            <i class="fas fa-check-circle"></i> No mixed-up punches found for {{ $month }} {{ $year }}.
                        </div>
                    @else
                        <form method="POST" action="{{ route('admin.attendance.repair.apply') }}"
                              onsubmit="return confirm('Rebuild the selected employees\' punches for {{ $month }} {{ $year }}?')">
                            @csrf
                            <input type="hidden" name="month" value="{{ $month }}">
                            <input type="hidden" name="year" value="{{ $year }}">

                            <p class="mb-2">
                                <strong>{{ count($plans) }}</strong> employee(s) need fixing. Review each one, untick anyone you don't want changed, then Apply.
                                Duties longer than 12 hours are highlighted — check them, they may be a forgotten OUT.
                            </p>

                            @foreach($plans as $plan)
                                @php $emp = $plan['employee']; @endphp
                                <div class="repair-card">
                                    <div class="repair-card-head">
                                        <input class="form-check-input mt-0" type="checkbox" name="employee_ids[]" value="{{ $emp->id }}" id="repair_{{ $emp->id }}" checked>
                                        <label for="repair_{{ $emp->id }}" class="fw-semibold mb-0">{{ $emp->name }}</label>
                                        <small class="text-muted">{{ count($plan['before']) }} record(s) → {{ count($plan['after']) }} duty(s)</small>
                                        @if($plan['ignored'])
                                            <span class="badge bg-warning text-dark">{{ $plan['ignored'] }} repeat tap(s) removed</span>
                                        @endif
                                    </div>
                                    <div class="repair-cols">
                                        <div>
                                            <div class="text-danger fw-semibold small mb-1">Now (wrong)</div>
                                            <table class="table table-sm">
                                                <thead><tr><th>Date</th><th>IN</th><th>OUT</th><th>Time</th></tr></thead>
                                                <tbody>
                                                @foreach($plan['before'] as $row)
                                                    @php $r = $fmt($row); @endphp
                                                    <tr>
                                                        <td>{{ $r['date'] }}</td>
                                                        <td>{{ $r['in'] }}</td>
                                                        <td>{!! $r['out'] ? e($r['out']) : '<span class="badge bg-danger">Open</span>' !!}</td>
                                                        <td>{{ $r['minutes'] !== null ? AttendanceSummary::formatMinutes($r['minutes']) : '—' }}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div>
                                            <div class="text-success fw-semibold small mb-1">After fix</div>
                                            <table class="table table-sm">
                                                <thead><tr><th>Date</th><th>IN</th><th>OUT</th><th>Time</th></tr></thead>
                                                <tbody>
                                                @foreach($plan['after'] as $row)
                                                    @php $r = $fmt($row); @endphp
                                                    <tr>
                                                        <td>{{ $r['date'] }}</td>
                                                        <td>{{ $r['in'] }}</td>
                                                        <td>{!! $r['out'] ? e($r['out']) : '<span class="badge bg-secondary">No OUT</span>' !!}</td>
                                                        <td class="{{ ($r['minutes'] ?? 0) > 720 ? 'repair-long' : '' }}">
                                                            {{ $r['minutes'] !== null ? AttendanceSummary::formatMinutes($r['minutes']) : '—' }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-tools"></i> Apply Fix for Selected
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
