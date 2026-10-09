<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daily Attendance Sheet - {{ $date->format('d M Y') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; line-height: 1.2; color: #212529; background-color: #fff; }
        .container { width: 98%; margin: 0 auto; padding: 4px; }
        .header { display: table; width: 100%; padding: 6px; border: 1px solid #000; }
        .header-left { display: table-cell; width: 25%; vertical-align: middle; }
        .header-right { display: table-cell; width: 75%; vertical-align: middle; }
        .header-right h1 { margin: 0; font-size: 18px; }
        .header-right p { margin: 0; font-size: 10px; }
        h4.title { font-size: 16px; font-weight: bold; text-align: center; margin: 10px 0 2px 0; }
        .subtitle { text-align: center; font-size: 11px; margin-bottom: 8px; }
        .totals { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .totals td { border: 1px solid #999; padding: 4px 6px; font-size: 10px; text-align: center; }
        .totals strong { display: block; font-size: 13px; }
        table.sheet { width: 100%; border-collapse: collapse; }
        table.sheet th, table.sheet td { border: 1px solid #999; padding: 5px 5px; font-size: 10px; text-align: left; vertical-align: middle; }
        table.sheet th { background-color: #e9ecef; font-weight: bold; }
        .absence { color: #b91c1c; font-weight: bold; }
        .present { color: #166534; font-weight: bold; }
        .muted { color: #6c757d; }
        .sign { width: 14%; height: 24px; }
        .footer { width: 100%; margin-top: 40px; display: table; }
        .footer div { display: table-cell; width: 50%; font-size: 11px; }
        .footer .right { text-align: right; }
        .sign-line { display: inline-block; border-top: 1px solid #000; padding-top: 3px; min-width: 160px; text-align: center; }
    </style>
</head>
<body>
@php
    use App\Services\EmployeeAttendanceSummaryService as AttendanceSummary;

    $statusLabels = ['present' => 'Present', 'absence' => 'Absent', 'leave' => 'Leave', 'off_day' => 'Weekly Off', 'upcoming' => 'Upcoming'];
    $totals = $sheet['totals'];
    $logo = \App\Models\Setting::get('logo');
    $logoPath = $logo ? public_path('images/' . $logo) : null;
@endphp
<div class="container">
    <div class="header">
        <div class="header-left">
            @if($logoPath && file_exists($logoPath))
                <img src="{{ $logoPath }}" alt="Logo" style="height: 55px;">
            @endif
        </div>
        <div class="header-right">
            <h1>{{ \App\Models\Setting::get('company_name') }}</h1>
            <p>{!! \App\Models\Setting::get('address') !!}</p>
            <p>Mobile: {{ \App\Models\Setting::get('phone_one') }}{{ \App\Models\Setting::get('phone_two') ? ', ' . \App\Models\Setting::get('phone_two') : '' }}</p>
        </div>
    </div>

    <h4 class="title">Daily Attendance Sheet</h4>
    <p class="subtitle">
        Date: <strong>{{ $date->format('d M Y') }}</strong> ({{ $sheet['day_name'] }})
        @if($status) · Filter: {{ $statusLabels[$status] ?? $status }} @endif
    </p>

    <table class="totals">
        <tr>
            <td>Employees<strong>{{ $totals['employees'] }}</strong></td>
            <td>Present<strong>{{ $totals['present'] }}</strong></td>
            <td>Absent<strong>{{ $totals['absence'] }}</strong></td>
            <td>Leave<strong>{{ $totals['leave'] }}</strong></td>
            <td>Weekly Off<strong>{{ $totals['off_day'] }}</strong></td>
            <td>Out Missing<strong>{{ $totals['open'] }}</strong></td>
            <td>Total Hours<strong>{{ number_format($totals['hours'], 2) }}</strong></td>
        </tr>
    </table>

    <table class="sheet">
        <thead>
        <tr>
            <th style="width: 4%;">#</th>
            <th style="width: 22%;">Employee</th>
            <th style="width: 10%;">Status</th>
            <th style="width: 9%;">In</th>
            <th style="width: 9%;">Out</th>
            <th style="width: 8%;">Worked</th>
            <th style="width: 7%;">Short</th>
            <th style="width: 17%;">Note</th>
            <th class="sign">Signature</th>
        </tr>
        </thead>
        <tbody>
        @forelse($sheet['rows'] as $row)
            @php $emp = $row['employee']; @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    {{ $emp->name }}
                    @if($emp->designation)<br><span class="muted">{{ $emp->designation }}</span>@endif
                </td>
                <td class="{{ in_array($row['status'], ['absence', 'present'], true) ? $row['status'] : '' }}">
                    {{ $statusLabels[$row['status']] ?? $row['status'] }}
                    @if($row['leave_label'])<br><span class="muted">{{ $row['leave_label'] }}</span>@endif
                </td>
                <td>{{ $row['first_in']?->format('h:i A') ?? '' }}</td>
                <td>{{ $row['open_sessions'] > 0 ? 'Not out' : ($row['last_out']?->format('h:i A') ?? '') }}</td>
                <td>{{ $row['worked_minutes'] ? AttendanceSummary::formatMinutes($row['worked_minutes']) : '' }}</td>
                <td>{{ $row['short_minutes'] > 0 ? AttendanceSummary::formatMinutes($row['short_minutes']) : '' }}</td>
                <td>{{ $row['notes'] }}</td>
                <td class="sign"></td>
            </tr>
        @empty
            <tr><td colspan="9" style="text-align:center;">No employee found.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div><span class="sign-line">Prepared By</span></div>
        <div class="right"><span class="sign-line">Authorized Signature</span></div>
    </div>
    <p class="muted" style="font-size: 9px; margin-top: 10px;">Printed: {{ now('Asia/Dhaka')->format('d M Y h:i A') }}</p>
</div>
</body>
</html>
