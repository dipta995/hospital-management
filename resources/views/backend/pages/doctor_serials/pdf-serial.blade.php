<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Doctor Serials</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
            font-size: 22px;
            text-transform: uppercase;
        }

        .info {
            margin-bottom: 15px;
            font-size: 14px;
        }

        .info p {
            margin: 4px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        th, td {
            border: 1px solid #000;
            padding: 8px 6px;
            text-align: center;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .print-button {
            display: inline-block;
            margin-bottom: 15px;
            padding: 8px 16px;
            font-size: 14px;
            background-color: #3498db;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            float: right;
        }

        @media print {
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>

<button class="print-button" onclick="window.print()">🖨️ Print</button>

<h2>Doctor Serials List</h2>

<div class="info">
    <p><strong>Reefer:</strong> {{ \App\Models\Reefer::find(request('reefer_id'))->name ?? '-' }}</p>
    <p><strong>Date:</strong> {{ \Carbon\Carbon::parse(request()->date)->format('Y-m-d') }}</p>
</div>

<table>
    <thead>
    <tr>
        <th>Serial No</th>
        <th>Patient Name</th>
        <th>Age</th>
        <th>Phone</th>
    </tr>
    </thead>
    <tbody>
    @foreach($datas as $item)
        <tr>
            <td>{{ $item->serial_number }}</td>
            <td>{{ $item->patient_name }}</td>
            <td>{{ $item->patient_age_year }}</td>
            <td>{{ $item->patient_phone }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

</body>
</html>
