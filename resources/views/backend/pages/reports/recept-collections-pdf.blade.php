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
                <img src="{{ asset('images/'.\App\Models\Setting::get('logo')) }}" alt="Logo" style="height: 80px;">
            </div>
            <div style=" display: table-cell; width: 60%; text-align: left; vertical-align: middle; color:#000;">
                <h1 style="margin: 0; font-size: 20px;">{{ \App\Models\Setting::get('company_name') }}</h1>
                <p style="margin: 0; font-size: 10px;">{!! \App\Models\Setting::get('address') !!}</p>
                <p style="margin: 0; font-size: 10px;">Mobile: {{ \App\Models\Setting::get('phone_one') }}
                    , {{ \App\Models\Setting::get('phone_two') }}</p>
                <p style="margin: 0; font-size: 10px;">Email: {{ \App\Models\Setting::get('email') }}</p>
            </div>
        </div>
            <h4 class="card-title" style="font-size: 20px; font-weight: bold; text-align: center; margin-top:10px;">{{ $pageHeader['title'] }}'s Report</h4>
        <p class="text-end" style="font-size: 14px; margin: 20px 150px;">
            <span style=" border: 1px solid black;border-radius: 10px;" class="text-white p-1 ">Collection : <span>{{ $overall_total_collection }} </span></span>
            | <span style=" border: 1px solid black;border-radius: 10px;" class="text-white p-1 "> Discount : <span>{{ $overall_total_discount }}</span></span>
            | <span style=" border: 1px solid black;border-radius: 10px;" class="text-white p-1 ">  Due : <span>{{ $overall_total_amount- $overall_total_collection }}</span></span>
        </p>
        <div class="table-responsive">
            <table class="table table-striped mt-3">
                <thead>

                </thead>
                <tbody>
                @foreach($datas as $date => $invoices)
                    <tr class="table-info">
                        <td colspan="3"><strong>Date: {{ $date }}</strong> </td>
                        <td colspan="2"><strong>Sub.  {{ $invoices->sum('total_amount') +  $invoices->sum('total_discount') }}</strong></td>
                        <td colspan="1"><strong>Dis.  {{ $invoices->sum('total_discount') }}</strong></td>
                        <td colspan="1"><strong>Col.  {{ $invoices->sum('total_collection') }}</strong></td>
                    </tr>
                    <tr>
                        <th>#</th>
{{--                        <th>Invoice Number</th>--}}
                        <th>Test's</th>
                        <th>Sub Total</th>
                        <th>Discount</th>
                        <th>Collection</th>
                        <th>Doctor</th>
                        <th>Refer</th>
                    </tr>

                    @foreach($invoices as $invoice_id => $group)
                        @php
                            $invoice = isset($group['data']) ? collect($group['data'])->first()->recept ?? null : null;
                        @endphp
                        <tr class="table-warning">
                            <td colspan="8"><strong>Recept: {{ $invoice->id ?? 'N/A' }}</strong></td>
                        </tr>

                        @if(isset($group['data']))
                            @foreach($group['data'] as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
{{--                                    <td>{{ $invoice->invoice_number ?? 'N/A' }}</td>--}}
                                    <td>
                                        @foreach($invoice->receptList ?? [] as $pr)
                                            {{ $pr->service->name }}
                                        @endforeach

                                    </td>
                                    <td>{{ $invoice->total_amount + $invoice->discount_amount ?? 0 }}</td>
                                    <td>{{ $invoice->discount_amount ?? 0 }}</td>
                                    <td>{{ $item->paid_amount }}</td>
                                    <td>{{ $invoice->admit->drreefer->name ?? 'N/A' }}</td>
                                    <td>{{ $invoice->admit->reefer->name ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
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
        width: 90%;
        margin: 5px 0;
        border-collapse: collapse;
    }

    table th, table td {
        border: 1px solid #ddd;
        padding: 6px 10px;
        text-align: left;
        font-size: 12px;
    }

    table th {
        background-color: #f8f9fa;
        color: #495057;
        font-weight: bold;
    }

    table td {
        background-color: #fff;
        padding: -10px;
        font-size: 12px;
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
