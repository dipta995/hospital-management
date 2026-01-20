<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.2;
            width: 950px;
            margin: 0 auto;
            position: relative;
        }

        .watermark {
            position: fixed;
            top: 30%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg); /* Centered and rotated */
            font-size: 150px; /* Increased size */
            color: rgba(0, 0, 0, 0.15); /* Adjusted transparency for a softer look */
            font-weight: bold;
            z-index: 11;
            pointer-events: none; /* Make it non-interactive */
        }

        .header {
            display: flex;
            align-items: flex-start; /* keep items aligned at top */
            justify-content: space-between;
            border: 1px solid black;
            border-bottom: 2px solid black;
            padding: 10px;
            margin-top: 5px;
        }

        .header-left img {
            height: 80px;
        }

        .header-center {
            text-align: center;
            flex: 1;
            margin: 0 10px;
        }

        .header-center h1 {
            margin: 0;
            font-size: 15px;
            font-weight: bold;
            text-align: center; /* force center */
        }

        .header-center p {
            margin: 0;
            font-size: 11px;
            font-weight: 600;
            text-align: center;
        }

        .header-right img {
            width: 80px;
            margin-top: 10px;
        }

        .title {
            text-align: center;
            margin: 10px 0;
            font-size: 16px;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }

        .details, .tests {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .details th, .details td, .tests th, .tests td {
            border: 1px solid black;
            padding: 4px;
            text-align: left;
            font-size: 10px;
        }

        .details th, .tests th {
            background-color: #f2f2f2;
            text-align: center;
        }

        .tests th {
            font-size: 12px;
        }

        .details td {
            /*font-weight: bold;*/
        }

        .badge-status {
            display: inline-block;
            padding: 6px 18px;
            border-radius: 16px;
            border: 2px solid #000;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .watermark-paid {
            position: fixed;
            top: 35%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-25deg);
            font-size: 140px;
            font-weight: 900;
            color: rgba(0, 128, 0, 0.12);
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
            color: rgba(255, 0, 0, 0.15);
            text-transform: uppercase;
            pointer-events: none;
            z-index: 0;
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
@if($due > 0)
    <div class="watermark-due">DUE</div>
@else
    <div class="watermark-paid">PAID</div>
@endif
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
        <p style="margin-top: 10px;">{!! \App\Models\Setting::get('address') !!}</p>
        <p>Mobile: {{ \App\Models\Setting::get('phone_one') }}, {{ \App\Models\Setting::get('phone_two') }}</p>
        <p>Email: {{ \App\Models\Setting::get('email') }}</p>
    </div>
    <div class="header-right">
        <img src="data:image/png;base64,{{ $qrcode }}" alt="QR Code">
    </div>
</div>

{{--<hr style="background-color: black; margin-top: 3px;">--}}


<div class="title">Money Receipt</div>
<table class="details">
    <tr>
        <th>Patient ID</th>
        <td><strong>{{ $recept->user->id }}</strong></td>
        <th>Date</th>
        <td>{{ \Carbon\Carbon::parse($recept->created_at)->setTimezone('Asia/Dhaka')->format('d-m-Y h:i A') }}</td>
    </tr>
    <tr>
        <th>Bill No</th>
        <td><strong>{{ $recept->id }}</strong></td>
        <th>Discount By</th>
        <td>{{ $recept->discount_by }}</td>
    </tr>
    <tr>
        <th>Patient's Name</th>
        <td>{{ $recept->user->name }}</td>
        <th>Mobile</th>
        <td>{{ $recept->user->phone }}</td>
    </tr>
    <tr>
        <th>Age</th>
        <td>{{ $recept->user->age }} </td>
        <th>B.Group</th>
        <td>{{ $recept->user->blood_group }}</td>
    </tr>
    <tr>
        <th>Gender</th>
        <td>{{ $recept->user->gender }}</td>
        <th>Address</th>
        <td>{{ $recept->user->address }}</td>
    </tr>
{{--    <tr>--}}
{{--        <th>Doctor name</th>--}}
{{--        <td colspan="3">{{ $recept->reeferDr->name ?? $recept->dr_name }}</td>--}}
{{--    </tr>--}}
</table>
<table class="tests">
    <thead>
    <tr>
{{--        <th style="text-align: left; width: 60px;">Code</th>--}}
        <th>Service</th>
        <th style="text-align: right; width: 70px;">Amount (Tk)</th>
    </tr>
    </thead>
    <tbody>
    @foreach($recept->receptList as $key=>$item)
        <tr>

            <td> <strong>{{ $item->service->name }}</strong></td>
            <td style="text-align: right; width: 70px;">{{ number_format($item->price, 2) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<div style="margin-top: 15px; width: 100%; overflow: hidden;">
    <div style="width: 120px; float: left; text-align: center; padding-top: 10px;">
        <span class="badge-status">
            @if($due > 0) DUE @else PAID @endif
        </span>
    </div>
    <div style="width: 280px; float: right;">
        <table class="details" style="margin-bottom: 4px;">
            <tr>
                <th style="text-align: right; width: 50%;">Sub Total</th>
                <td style="text-align: right; width: 50%;">{{ number_format($total, 2) }}</td>
            </tr>
            <tr>
                <th style="text-align: right;">Less [-]</th>
                <td style="text-align: right;">{{ number_format($discount, 2) }}</td>
            </tr>
            <tr>
                <th style="text-align: right;">Received Amount</th>
                <td style="text-align: right;">{{ number_format($paid, 2) }}</td>
            </tr>
            <tr>
                <th style="text-align: right;">Due</th>
                <td style="text-align: right;">{{ number_format($due, 2) }}</td>
            </tr>
        </table>
    </div>
</div>
<p style="font-size: 12px;">
    <strong style="text-align: left; float: left;">Posting By : {{ $recept->admin->name ?? 'N/A' }}</strong>

</p>
<br>


{{--<img src="{{ asset('note.png') }}" alt="Logo" style="height: 20px;">--}}

{{--{!! \App\Models\Setting::get('footer_invoice') !!}--}}
{{--<span style="text-align: left; font-size: 6px;"> {{ $recept->id }}</span>--}}

</body>
</html>
