<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>{{ $pageHeader['title'] }} - Day Wise Balance</title>
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
                Day Wise Balance
            </h4>
            <button onclick="window.print()" class="btn btn-primary" style="font-size:14px;">Print</button>
        </div>

        @php
            $branchId = auth()->user()->branch_id;
            $fromDate = request('from_date');
            $toDate = request('to_date');

            $start = $fromDate ? \Carbon\Carbon::parse($fromDate)->startOfDay() : \Carbon\Carbon::now('Asia/Dhaka')->startOfMonth();
            $end = $toDate ? \Carbon\Carbon::parse($toDate)->endOfDay() : \Carbon\Carbon::now('Asia/Dhaka')->endOfMonth();

            $rows = [];
            $totalDiagnostic = 0;
            $totalHospital = 0;
            $totalEarn = 0;
            $totalCost = 0;
            $totalBalance = 0;

            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $day = $date->toDateString();

                $diagnostic = \App\Models\InvoicePayment::with(['invoice'])
                    ->whereHas('invoice', function ($q) use ($branchId) {
                        $q->where('branch_id', $branchId);
                    })
                    ->whereDate('creation_date', $day)
                    ->sum('paid_amount');

                $hospital = \App\Models\ReceptPayment::whereHas('admit', function ($q) use ($branchId) {
                        $q->where('branch_id', $branchId);
                    })
                    ->whereDate('creation_date', $day)
                    ->sum('paid_amount');

                $earn = \App\Models\Earn::where('branch_id', $branchId)
                    ->whereDate('date', $day)
                    ->sum('amount');

                $cost = \App\Models\Cost::where('branch_id', $branchId)
                    ->whereDate('creation_date', $day)
                    ->sum('amount');

                $balance = $diagnostic + $hospital + $earn - $cost;

                $rows[] = [
                    'date' => $date->format('d M Y'),
                    'diagnostic' => $diagnostic,
                    'hospital' => $hospital,
                    'earn' => $earn,
                    'cost' => $cost,
                    'balance' => $balance,
                ];

                $totalDiagnostic += $diagnostic;
                $totalHospital += $hospital;
                $totalEarn += $earn;
                $totalCost += $cost;
                $totalBalance += $balance;
            }
        @endphp

        <!-- Day-wise Financial Summary -->
        <div class="table-responsive" style="margin-top: 10px;">
            <table class="table">
                <thead>
                <tr>
                    <th>Date</th>
                    <th class="text-end">Diagnostic Collection</th>
                    <th class="text-end">Hospital Collection</th>
                    <th class="text-end">Earn</th>
                    <th class="text-end">Cost</th>
                    <th class="text-end">Balance</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr>
                        <td>{{ $row['date'] }}</td>
                        <td class="text-end">৳ {{ number_format($row['diagnostic'], 2) }}</td>
                        <td class="text-end">৳ {{ number_format($row['hospital'], 2) }}</td>
                        <td class="text-end">৳ {{ number_format($row['earn'], 2) }}</td>
                        <td class="text-end">৳ {{ number_format($row['cost'], 2) }}</td>
                        <td class="text-end">৳ {{ number_format($row['balance'], 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <th>Total</th>
                    <th class="text-end">৳ {{ number_format($totalDiagnostic, 2) }}</th>
                    <th class="text-end">৳ {{ number_format($totalHospital, 2) }}</th>
                    <th class="text-end">৳ {{ number_format($totalEarn, 2) }}</th>
                    <th class="text-end">৳ {{ number_format($totalCost, 2) }}</th>
                    <th class="text-end">৳ {{ number_format($totalBalance, 2) }}</th>
                </tr>
                </tfoot>
            </table>
        </div>

    </div>
</div>

<style>
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

    .container {
        width: 95%;
        margin: 0 auto;
        padding: 20px;
    }

    .container {
        width: 99%;
        margin: 0 auto;
        padding: 10px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        font-size: 12px;
    }

    table th, table td {
        border: 1px solid #ddd;
        padding: 6px 8px;
    }

    table th {
        background-color: #f8f9fa;
        font-weight: bold;
    }

    .text-end {
        text-align: right;
    }

    .btn {
        padding: 6px 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        display: inline-block;
        text-decoration: none;
        text-align: center;
        font-size: 12px;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
    }

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
