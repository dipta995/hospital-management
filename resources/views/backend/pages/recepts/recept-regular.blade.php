<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1;
            width: 500px;
            margin: 0 auto;
            position: relative;
            /*transform: rotate(90deg);*/

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
            text-align: left;
            margin-left: 20px;
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
        }

        .details, .tests {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
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

        .financial-summary {
            text-align: right;
            font-size: 14px;
            margin-right: 20px;
        }

        .financial-summary span {
            font-weight: bold;
        }
    </style>
</head>
<body>
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
        <th style="text-align: right; width: 70px;">Amount (tk)</th>
    </tr>
    </thead>
    <tbody>
    @foreach($recept->receptList as $key=>$item)
        <tr>

            <td> <strong>{{ $item->service->name }}</strong></td>
            <td style="text-align: right; width: 70px;">{{ $item->price }}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot style="margin-top: 10px;">
    <tr>
        <td colspan="3" style="height: 5px; border: none;">
        </td>
    </tr>
    <tr>
        <td style="height: 5px; border: none;"></td>
        <th style="text-align: right;">Sub Total</th>
        <th style="text-align: right;">{{ $recept->total_amount+$recept->discount_amount }}</th>
    </tr>
    <tr>
        <td style="height: 5px; border: none;">
            <div style="width: 100px; float: right;">
                <h1 style="text-align: center; border: 2px solid black; border-radius: 25%;">
                    @if($recept->total_amount-$recept->receptPayments->sum('paid_amount')>0) Due @else Paid @endif

                </h1>
            </div>
        </td>
        <th style="text-align: right;">Less [-]</th>
        <th style="text-align: right;">{{ $recept->discount_amount }}</th>
    </tr>
    <tr>
        <td style="height: 5px; border: none;"></td>
        <th style="text-align: right;">Received Amount</th>
        <th style="text-align: right;">{{ $recept->receptPayments->sum('paid_amount') }}</th>
    </tr>
    <tr>
        <td style="height: 5px; border: none;"></td>
        <th style="text-align: right;">Due</th>
        <th style="text-align: right;">{{ $recept->total_amount-$recept->receptPayments->sum('paid_amount') }}</th>
    </tr>

    </tfoot>
</table>
<p style="font-size: 12px;">
    <strong style="text-align: left; float: left;">Posting By : {{ $recept->admin->name ?? 'N/A' }}</strong>

</p>
<br>


{{--<img src="{{ asset('note.png') }}" alt="Logo" style="height: 20px;">--}}

{{--{!! \App\Models\Setting::get('footer_invoice') !!}--}}
{{--<span style="text-align: left; font-size: 6px;"> {{ $recept->id }}</span>--}}

</body>
</html>
