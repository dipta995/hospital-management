<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<div class="container">

    <div class="card-body">

        <div class="header"
             style="display: table; width: 100%; border-bottom: 2px solid black; padding-bottom: 10px; border: 1px solid black;">
            <div style="display: table-cell; width: 40%; vertical-align: middle;">
                <img src="{{ public_path('images/'.\App\Models\Setting::get('logo')) }}" alt="Logo"
                     style="height: 80px;">
            </div>
            <div style=" display: table-cell; width: 60%; text-align: left; vertical-align: middle; color:#000;">
                <h1 style="margin: 0; font-size: 20px;">{{ \App\Models\Setting::get('company_name') }}</h1>
                <p style="margin: 0; font-size: 10px;">{!! \App\Models\Setting::get('address') !!}</p>
                <p style="margin: 0; font-size: 10px;">Mobile: {{ \App\Models\Setting::get('phone_one') }}
                    , {{ \App\Models\Setting::get('phone_two') }}</p>
                <p style="margin: 0; font-size: 10px;">Email: {{ \App\Models\Setting::get('email') }}</p>
            </div>
        </div>
        <h4 class="card-title"
            style="font-size: 20px; font-weight: bold; text-align: center; margin-top:10px;">{{ $pageHeader['title'] }}
            's Report</h4>
        <h5 style="float: right;">{{ $startDate }} to {{ $endDate }}</h5>
        <p>
            <strong>Total:{{ $totalAmount }}</strong> ||
            <strong>Paid:{{ $totalPaidAmount }}</strong> ||
            <strong>Due:{{ $totalDueAmount }}</strong>
        </p>
        <div class="table-responsive">
            <table class="table table-striped mt-3">
                <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Refer By</th>
                    <th>Dr</th>
                    <th>Patient</th>
                    <th>Amount</th>
                    <th>Paid</th>
                    <th>Unpaid</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @foreach($datas as $item)
                    <tr>
                        <td>{{ $item->id }} | {{ $item->invoice_number }} <br>
                        {{ $item->creation_date }}
                        </td>
                        <td>{{ $item->reeferBy->name ?? 'n/a' }}</td>
                        <td>{{ $item->reeferDr->name ?? 'n/a' }}</td>
                        <td>
                            {{ $item->patient_name }}
                            <br>
                            <strong>Total:</strong>{{ $item->total_amount+$item->discount_amount }}</br>
                            <strong>Less:</strong>{{ $item->discount_amount }}
                        </td>
                        <td>{{ $item->refer_fee_total  }}</td>
                        <td>{{ $item->costs->sum('amount') }}</td>
                        <td>{{ $item->refer_fee_total - ($item->costs->sum('amount')) }} @if($item->refer_fee_total - ($item->costs->sum('amount'))<0) Extra @endif</td>
                        <td>
                            @if($item->refer_fee_total - ($item->costs->sum('amount'))>0)
                                <strong class="badge bg-danger">Unpaid</strong>
                            @else
                                <strong class="badge bg-info">Paid</strong>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
    </div>
</div>

<style>
    /* General reset */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        line-height: 1.0;
        color: #212529;
        background-color: #fff;
        margin: 0 auto;
    }

    h1, h2, h3, h4, h5, h6 {
        margin-bottom: 0px;
        color: #333;
    }

    /* Container */
    .container {
        width: 99%;
        margin: 0 auto;
        padding: 2px;
    }

    /* Table Styles */
    table {
        width: 100%;
        margin: 0 auto;
        border-collapse: collapse;
    }

    table th, table td {
        border: 1px solid #ddd;
        padding: 6px 10px;
        text-align: left;
    }

    table th {
        background-color: #f8f9fa;
        color: #495057;
        font-weight: bold;
    }

    table td {
        background-color: #fff;
    }

    /* Table striped rows */
    table tr:nth-child(odd) {
        background-color: #f2f2f2;
    }

    /* Table hover effect */
    table tr:hover {
        background-color: #e9ecef;
    }

    /* Button styles (you can remove if you don't need them) */
    .btn {
        display: inline-block;
        font-size: 12px;
        padding: 8px 16px;
        margin: 5px;
        text-align: center;
        cursor: pointer;
        border: 1px solid transparent;
        border-radius: 4px;
        text-decoration: none;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
        border-color: #dc3545;
    }

    /* Text utility classes */
    .text-center {
        text-align: center;
    }

    .text-end {
        text-align: right;
    }

    .text-left {
        text-align: left;
    }

    /* Background Colors */
    .bg-info {
        background-color: #17a2b8;
        color: white;
    }

    .bg-danger {
        background-color: #dc3545;
        color: white;
    }

    .bg-gray {
        background-color: #f8f9fa;
    }

    /* Padding helpers */
    .p-1 {
        padding: 5px;
    }

    .p-2 {
        padding: 10px;
    }

    .p-3 {
        padding: 15px;
    }

    /* Margin helpers */
    .m-1 {
        margin: 5px;
    }

    .m-2 {
        margin: 10px;
    }

    .m-3 {
        margin: 15px;
    }
</style>

</body>
</html>
