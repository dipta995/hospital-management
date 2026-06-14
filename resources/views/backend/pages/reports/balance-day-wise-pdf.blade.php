@extends('backend.layouts.report-pdf-layout')

@section('pdf-title')
    Day-wise Balance Report
@endsection

@section('pdf-subtitle')
    Daily breakdown: Diagnostic + Hospital collection + Earn − Cost = Balance
@endsection

@section('pdf-period')
    @if(request('from_date') || request('to_date'))
        {{ request('from_date') ?: 'Start of month' }} → {{ request('to_date') ?: 'End of month' }}
    @else
        Current month
    @endif
@endsection

@section('pdf-actions')
    <button onclick="window.print()" class="rpdf-btn">Print / Save as PDF</button>
@endsection

@section('content')
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

        $fmt = fn ($n) => number_format((float) $n, 2);
    @endphp

    <table class="rpdf-kpi-table">
        <tr>
            <td>
                <div class="rpdf-kpi-label">Diagnostic</div>
                <div class="rpdf-kpi-value">৳ {{ $fmt($totalDiagnostic) }}</div>
            </td>
            <td>
                <div class="rpdf-kpi-label">Hospital</div>
                <div class="rpdf-kpi-value">৳ {{ $fmt($totalHospital) }}</div>
            </td>
            <td>
                <div class="rpdf-kpi-label">Earn</div>
                <div class="rpdf-kpi-value success">৳ {{ $fmt($totalEarn) }}</div>
            </td>
            <td>
                <div class="rpdf-kpi-label">Cost</div>
                <div class="rpdf-kpi-value danger">৳ {{ $fmt($totalCost) }}</div>
            </td>
        </tr>
    </table>

    <table class="rpdf-table">
        <thead>
        <tr>
            <th>Date</th>
            <th class="text-end">Diagnostic</th>
            <th class="text-end">Hospital</th>
            <th class="text-end">Earn</th>
            <th class="text-end">Cost</th>
            <th class="text-end">Balance</th>
        </tr>
        </thead>
        <tbody>
        @foreach($rows as $row)
            <tr>
                <td>{{ $row['date'] }}</td>
                <td class="text-end">৳ {{ $fmt($row['diagnostic']) }}</td>
                <td class="text-end">৳ {{ $fmt($row['hospital']) }}</td>
                <td class="text-end">৳ {{ $fmt($row['earn']) }}</td>
                <td class="text-end">৳ {{ $fmt($row['cost']) }}</td>
                <td class="text-end fw-bold">৳ {{ $fmt($row['balance']) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr class="rpdf-row-total">
            <th>Period Total</th>
            <th class="text-end">৳ {{ $fmt($totalDiagnostic) }}</th>
            <th class="text-end">৳ {{ $fmt($totalHospital) }}</th>
            <th class="text-end">৳ {{ $fmt($totalEarn) }}</th>
            <th class="text-end">৳ {{ $fmt($totalCost) }}</th>
            <th class="text-end">৳ {{ $fmt($totalBalance) }}</th>
        </tr>
        </tfoot>
    </table>
@endsection
