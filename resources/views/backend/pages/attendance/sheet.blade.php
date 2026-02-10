<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Attendance Sheet</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            line-height: 1.1;
            color: #212529;
            background-color: #fff;
        }
        .container {
            width: 98%;
            margin: 0 auto;
            padding: 4px;
        }
        .header {
            display: table;
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            border: 1px solid #000;
        }
        .header-left {
            display: table-cell;
            width: 30%;
            vertical-align: middle;
        }
        .header-right {
            display: table-cell;
            width: 70%;
            vertical-align: middle;
        }
        .header-right h1 {
            margin: 0;
            font-size: 18px;
        }
        .header-right p {
            margin: 0;
            font-size: 10px;
        }
        h4.title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin: 10px 0 6px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 4px 6px;
            font-size: 10px;
            text-align: left;
        }
        table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .date-row {
            background-color: #e9ecef;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="header-left">
            <img src="{{ public_path('images/'.\App\Models\Setting::get('logo')) }}" alt="Logo" style="height: 60px;">
        </div>
        <div class="header-right">
            <h1>{{ \App\Models\Setting::get('company_name') }}</h1>
            <p>{!! \App\Models\Setting::get('address') !!}</p>
            <p>Mobile: {{ \App\Models\Setting::get('phone_one') }}, {{ \App\Models\Setting::get('phone_two') }}</p>
            <p>Email: {{ \App\Models\Setting::get('email') }}</p>
        </div>
    </div>

    <h4 class="title">Attendance Sheet - {{ $month }} {{ $year }}</h4>

    <table>
        <thead>
        <tr>
            <th style="width: 18%;">Date</th>
            <th style="width: 10%;">Total Present</th>
            <th style="width: 32%;">Employee</th>
            <th style="width: 20%;">In Time</th>
            <th style="width: 20%;">Out Time</th>
        </tr>
        </thead>
        <tbody>
        @php use Carbon\Carbon; @endphp
        @forelse($groupedAttendances ?? [] as $date => $items)
            <tr class="date-row">
                <td>{{ Carbon::parse($date)->format('d M Y') }}</td>
                <td>{{ $items->count() }}</td>
                <td colspan="3"></td>
            </tr>
            @foreach($items as $attendance)
                <tr>
                    <td></td>
                    <td></td>
                    <td>{{ optional($attendance->employee)->name ?? '-' }}</td>
                    <td>{{ $attendance->in_time ? Carbon::parse($attendance->in_time)->format('h:i:s A') : '-' }}</td>
                    <td>{{ $attendance->out_time ? Carbon::parse($attendance->out_time)->format('h:i:s A') : '-' }}</td>
                </tr>
            @endforeach
        @empty
            <tr><td colspan="5">No attendance found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
</body>
</html>
