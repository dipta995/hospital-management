<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>{{ $pageHeader['title'] }}'s Balance</title>
</head>
<body>
<div class="container">

    <!-- Header -->
    <div class="card-body">

        <div class="header"
             style="display: table; width: 100%; border-bottom: 2px solid black; padding-bottom: 10px; margin-bottom: 20px;">
            <div style="display: table-cell; width: 40%; vertical-align: middle;">
                <img src="{{ public_path('images/'.\App\Models\Setting::get('logo')) }}" alt="Logo" style="height: 80px;">
            </div>
            <div style="display: table-cell; width: 60%; text-align: left; vertical-align: middle; color:#000;">
                <h1 style="margin: 0; font-size: 24px;">{{ \App\Models\Setting::get('company_name') }}</h1>
                <p style="margin: 0; font-size: 12px;">{!! \App\Models\Setting::get('address') !!}</p>
                <p style="margin: 0; font-size: 12px;">Mobile: {{ \App\Models\Setting::get('phone_one') }}, {{ \App\Models\Setting::get('phone_two') }}</p>
                <p style="margin: 0; font-size: 12px;">Email: {{ \App\Models\Setting::get('email') }}</p>
            </div>
        </div>

        <!-- Title and Print Button -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h4 style="font-size: 22px; font-weight: bold; text-align: center; margin: 0 auto;">
                {{ $pageHeader['title'] }}'s Balance
            </h4>
            <button onclick="window.print()" class="btn btn-primary" style="font-size:14px;">🖨️ Print</button>
        </div>

        <!-- Financial Summary -->
        <div class="financial-summary" style="display: flex; justify-content: center; margin-bottom: 20px;">
            {!! currentBalanceMonth() !!}
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
        line-height: 1.5;
        color: #212529;
        background-color: #fff;
    }

    /* Container */
    .container {
        width: 95%;
        margin: 0 auto;
        padding: 20px;
    }

    /* Table Styles */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    table th, table td {
        border: 1px solid #dee2e6;
        padding: 8px;
        text-align: left;
    }

    table th {
        background-color: #f8f9fa;
        font-weight: bold;
    }

    /* Buttons */
    .btn {
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        display: inline-block;
        text-decoration: none;
        text-align: center;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
    }

    /* Financial Summary Card */
    .financial-summary > div {
        background: #f1f1f1;
        border: 1px solid #ccc;
        border-radius: 12px;
        padding: 25px;
        margin-top: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    .financial-summary h5 {
        font-size: 18px;
        margin-bottom: 15px;
        color: #333;
    }

    .financial-summary p {
        font-size: 16px;
        margin-bottom: 8px;
        color: #444;
    }

    .financial-summary hr {
        margin: 10px 0;
    }

    /* Print Style */
    @media print {
        .btn {
            display: none;
        }

        body {
            margin: 0;
        }

        .container {
            padding: 0;
            width: 100%;
        }

        .card-body {
            margin: 0;
            border: none;
            box-shadow: none;
        }
    }
</style>

</body>
</html>
