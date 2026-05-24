<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money Receipt</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            line-height: 1.4;
            width: 500px;
            margin: 0 auto;
            position: relative;
            background: #fff;
            color: #333;
        }

        .watermark {
            position: fixed;
            top: 30%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 150px;
            color: rgba(0, 0, 0, 0.08);
            font-weight: bold;
            z-index: 0;
            pointer-events: none;
        }

        .watermark-paid {
            position: fixed;
            top: 35%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-25deg);
            font-size: 140px;
            font-weight: 900;
            color: rgba(0, 128, 0, 0.08);
            text-transform: uppercase;
            pointer-events: none;
            z-index: 0;
        }

        .watermark-due {
            position: fixed;
            top: 35%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-25deg);
            font-size: 140px;
            font-weight: 900;
            color: rgba(255, 0, 0, 0.08);
            text-transform: uppercase;
            pointer-events: none;
            z-index: 0;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 2px solid #333;
            border-bottom: 3px solid #333;
            padding: 12px;
            background: #fafafa;
        }

        .header-left img {
            height: 75px;
            max-width: 75px;
        }

        .header-center {
            text-align: center;
            flex: 1;
            margin: 0 15px;
            padding: 0 15px;
            border-left: 1px solid #ccc;
            border-right: 1px solid #ccc;
        }

        .header-center h1 {
            margin: 0 0 3px 0;
            font-size: 16px;
            font-weight: 700;
            color: #1a1a1a;
            letter-spacing: 0.5px;
        }

        .header-center p {
            margin: 2px 0;
            font-size: 9px;
            color: #555;
            line-height: 1.3;
        }

        .header-right img {
            width: 75px;
            height: 75px;
        }

        .title {
            text-align: center;
            margin: 12px 0 8px 0;
            padding: 6px 0;
            font-size: 18px;
            font-weight: 700;
            color: #1a1a1a;
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            font-size: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 8px;
        }

        .info-col {
            flex: 1;
        }

        .info-label {
            font-weight: 700;
            color: #555;
            font-size: 9px;
            margin-bottom: 1px;
        }

        .info-value {
            font-weight: 600;
            color: #1a1a1a;
            font-size: 10px;
        }

        .patient-section {
            background: #f9f9f9;
            border: 1px solid #ddd;
            padding: 8px;
            margin: 10px 0;
            font-size: 9px;
        }

        .patient-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 6px;
        }

        .patient-field {
            display: flex;
            flex-direction: column;
        }

        .patient-label {
            font-weight: 700;
            color: #666;
            font-size: 8px;
            text-transform: uppercase;
            margin-bottom: 2px;
            letter-spacing: 0.3px;
        }

        .patient-value {
            color: #1a1a1a;
            font-weight: 600;
            font-size: 10px;
        }

        .services-table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
            font-size: 10px;
        }

        .services-table th {
            background: #333;
            color: white;
            padding: 6px;
            text-align: left;
            font-weight: 700;
            border: 1px solid #333;
        }

        .services-table th:last-child {
            text-align: right;
        }

        .services-table td {
            padding: 6px;
            border: 1px solid #ddd;
            border-left: none;
            border-right: none;
        }

        .services-table td:last-child {
            text-align: right;
            font-weight: 600;
        }

        .services-table tr:last-child td {
            border-bottom: 2px solid #333;
        }

        .summary-section {
            margin: 12px 0;
            padding: 8px;
            background: #f9f9f9;
            border: 1px solid #ddd;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 10px;
        }

        .summary-label {
            font-weight: 600;
            color: #555;
        }

        .summary-value {
            font-weight: 700;
            color: #1a1a1a;
        }

        .summary-row.total {
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
            padding: 6px 0;
            margin: 6px 0;
            font-size: 11px;
        }

        .footer-section {
            margin-top: 10px;
            font-size: 9px;
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-top: 1px solid #ddd;
        }

        .footer-left, .footer-right {
            text-align: center;
        }

        .footer-label {
            font-weight: 700;
            color: #555;
            margin-bottom: 20px;
            height: 30px;
        }

        .footer-text {
            font-size: 8px;
            color: #666;
        }
    </style>
</head>
<body>
@php
    $total = $recept->total_amount ?? 0;
    $discount = $recept->discount_amount ?? 0;
    $paid = $recept->receptPayments->sum('paid_amount');
    $net = $total - $discount;
    $due = $net - $paid;
@endphp
<button onclick="window.print()" class="btn btn-primary no-print" style="background-color: #00a5bb; color: white;">🖨️ Print
</button>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
        }

    </style>

<div class="header">
    <div class="header-left">
        <img src="{{ asset('images/'.\App\Models\Setting::get('logo')) }}" alt="Logo">
    </div>
    <div class="header-center">
        <h1>{{ \App\Models\Setting::get('company_name') }}</h1>
        <p>{!! \App\Models\Setting::get('address') !!}</p>
        <p>Mobile: {{ \App\Models\Setting::get('phone_one') }}, {{ \App\Models\Setting::get('phone_two') }}</p>
        <p>Email: {{ \App\Models\Setting::get('email') }}</p>
    </div>
    <div class="header-right">
        <img src="data:image/png;base64,{{ $qrcode }}" alt="QR Code">
    </div>
</div>

<div class="title">Money Receipt</div>

<div class="receipt-info">
    <div class="info-col">
        <div class="info-label">Receipt No</div>
        <div class="info-value">{{ $recept->id }}</div>
    </div>
    <div class="info-col">
        <div class="info-label">Date & Time</div>
        <div class="info-value">{{ \Carbon\Carbon::parse($recept->created_at)->setTimezone('Asia/Dhaka')->format('d-m-Y H:i') }}</div>
    </div>
</div>

<div class="patient-section">
    <div class="patient-row">
        <div class="patient-field">
            <span class="patient-label">Patient ID</span>
            <span class="patient-value">{{ $recept->user->id }}</span>
        </div>
        <div class="patient-field">
            <span class="patient-label">Patient Name</span>
            <span class="patient-value">{{ $recept->user->name }}</span>
        </div>
    </div>
    <div class="patient-row">
        <div class="patient-field">
            <span class="patient-label">Age</span>
            <span class="patient-value">{{ $recept->user->age }} years</span>
        </div>
        <div class="patient-field">
            <span class="patient-label">Gender</span>
            <span class="patient-value">{{ ucfirst($recept->user->gender) }}</span>
        </div>
    </div>
    <div class="patient-row">
        <div class="patient-field">
            <span class="patient-label">Blood Group</span>
            <span class="patient-value">{{ $recept->user->blood_group ?? 'N/A' }}</span>
        </div>
        <div class="patient-field">
            <span class="patient-label">Mobile</span>
            <span class="patient-value">{{ $recept->user->phone }}</span>
        </div>
    </div>
    <div class="patient-row">
        <div class="patient-field" style="grid-column: 1 / -1;">
            <span class="patient-label">Address</span>
            <span class="patient-value">{{ $recept->user->address }}</span>
        </div>
    </div>
</div>

<table class="services-table">
    <thead>
        <tr>
            <th>Service Description</th>
            <th style="width: 80px;">Amount (Tk)</th>
        </tr>
    </thead>
    <tbody>
    @foreach($recept->receptList as $item)
        <tr>
            <td>{{ $item->service->name }}</td>
            <td>{{ number_format($item->price, 2) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="summary-section">
    <div class="summary-row">
        <span class="summary-label">Sub Total:</span>
        <span class="summary-value">{{ number_format($total, 2) }} Tk</span>
    </div>
    @if($discount > 0)
    <div class="summary-row">
        <span class="summary-label">Discount:</span>
        <span class="summary-value">- {{ number_format($discount, 2) }} Tk</span>
    </div>
    @endif
    <div class="summary-row total">
        <span class="summary-label">Net Total:</span>
        <span class="summary-value">{{ number_format($net, 2) }} Tk</span>
    </div>
    <div class="summary-row">
        <span class="summary-label">Paid Amount:</span>
        <span class="summary-value">{{ number_format($paid, 2) }} Tk</span>
    </div>
    @if($due > 0)
    <div class="summary-row" style="background: #ffe6e6; padding: 5px; margin: 5px -8px -8px -8px;">
        <span class="summary-label">Due Amount:</span>
        <span class="summary-value" style="color: #d9534f;">{{ number_format($due, 2) }} Tk</span>
    </div>
    @else
    <div class="summary-row" style="background: #e6ffe6; padding: 5px; margin: 5px -8px -8px -8px;">
        <span class="summary-label">Status:</span>
        <span class="summary-value" style="color: #5cb85c;">FULLY PAID</span>
    </div>
    @endif
</div>

<div class="footer-section">
    <div class="footer-left">
        <div class="footer-label"></div>
        <div class="footer-text">Prepared By<br>{{ $recept->admin->name ?? 'N/A' }}</div>
    </div>
    <div class="footer-right">
        <div class="footer-label"></div>
        <div class="footer-text">Authorized By<br>________________________</div>
    </div>
</div>

<div style="text-align: center; margin-top: 8px; font-size: 8px; color: #999; border-top: 1px solid #ddd; padding-top: 6px;">
    <p style="margin: 2px 0;">Thank you for your business</p>
    <p style="margin: 2px 0;">Receipt ID: {{ $recept->id }}</p>
</div>

</body>
</html>
