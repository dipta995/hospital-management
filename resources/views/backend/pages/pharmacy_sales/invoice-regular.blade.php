<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy Sale Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.2;
            width: 700px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            border: 1px solid #000;
            border-bottom: 2px solid #000;
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
            font-size: 18px;
            font-weight: bold;
        }
        .header-center p {
            margin: 0;
            font-size: 11px;
            font-weight: 600;
        }
        .title {
            text-align: center;
            margin: 10px 0;
            font-size: 16px;
            font-weight: bold;
            text-decoration: underline;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px;
            font-size: 11px;
        }
        th {
            background-color: #f2f2f2;
        }
        .no-border td, .no-border th {
            border: none;
        }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .totals td {
            font-weight: bold;
        }
        .footer {
            margin-top: 15px;
            font-size: 11px;
        }
        .no-print-btn {
            margin: 10px 0;
        }
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
<button onclick="window.print()" class="no-print no-print-btn">Print</button>

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
        <!-- reserved for QR or extra info if needed -->
    </div>
</div>

<div class="title">Pharmacy Sale Invoice</div>

<table class="details">
    <tr>
        <th class="text-left">Sale ID</th>
        <td class="text-left">{{ $sale->id }}</td>
        <th class="text-left">Date</th>
        <td class="text-left">{{ \Carbon\Carbon::parse($sale->sale_date)->setTimezone('Asia/Dhaka')->format('d-m-Y') }}</td>
    </tr>
    <tr>
        <th class="text-left">Customer</th>
        <td class="text-left">{{ optional($sale->customer)->name }}</td>
        <th class="text-left">Mobile</th>
        <td class="text-left">{{ optional($sale->customer)->phone }}</td>
    </tr>
    <tr>
        <th class="text-left">Doctor</th>
        <td class="text-left" colspan="3">{{ optional($sale->doctor)->name }}</td>
    </tr>
</table>

<table>
    <thead>
    <tr>
        <th class="text-left">#</th>
        <th class="text-left">Product</th>
        <th class="text-right">Qty</th>
        <th class="text-right">Unit Price</th>
        <th class="text-right">Discount</th>
        <th class="text-right">Total</th>
    </tr>
    </thead>
    <tbody>
    @foreach($sale->items as $index => $item)
        <tr>
            <td class="text-left">{{ $index + 1 }}</td>
            <td class="text-left">{{ optional($item->product)->name }}</td>
            <td class="text-right">{{ $item->quantity }}</td>
            <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
            <td class="text-right">{{ number_format($item->discount_amount, 2) }}</td>
            <td class="text-right">{{ number_format($item->total_amount, 2) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<table class="totals">
    <tr>
        <td class="text-right" style="border: none;" colspan="5">Subtotal</td>
        <td class="text-right">{{ number_format($sale->total_amount + $sale->discount_amount, 2) }}</td>
    </tr>
    <tr>
        <td class="text-right" style="border: none;" colspan="5">Invoice Discount</td>
        <td class="text-right">{{ number_format($sale->discount_amount, 2) }}</td>
    </tr>
    <tr>
        <td class="text-right" style="border: none;" colspan="5">Total</td>
        <td class="text-right">{{ number_format($sale->total_amount, 2) }}</td>
    </tr>
    <tr>
        <td class="text-right" style="border: none;" colspan="5">Paid</td>
        <td class="text-right">{{ number_format($sale->paid_amount, 2) }}</td>
    </tr>
    <tr>
        <td class="text-right" style="border: none;" colspan="5">Due</td>
        <td class="text-right">{{ number_format($sale->due_amount, 2) }}</td>
    </tr>
</table>

<div class="footer">
    <p>Note: {{ $sale->note }}</p>
</div>

</body>
</html>
