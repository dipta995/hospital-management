@extends('backend.layouts.report-pdf-layout')

@section('pdf-title')
    Cost Report — All Categories
@endsection

@section('pdf-subtitle')
    Total expenses per cost category
@endsection

@section('pdf-period')
    @php
        $startDate = request('start_date') ?? \Carbon\Carbon::today()->toDateString();
        $endDate = request('end_date') ?? \Carbon\Carbon::today()->toDateString();
    @endphp
    {{ $startDate }} → {{ $endDate }} · {{ ucfirst(request('type', 'diagnostic')) }}
@endsection

@section('pdf-actions')
    <button onclick="window.print()" class="rpdf-btn">Print / Save as PDF</button>
@endsection

@section('pdf-summary')
    @php $fmt = fn ($n) => number_format((float) $n, 2); @endphp
    <table class="rpdf-kpi-table">
        <tr>
            <td colspan="4">
                <div class="rpdf-kpi-label">Grand Total</div>
                <div class="rpdf-kpi-value danger">৳ {{ $fmt($totalAmount ?? 0) }}</div>
            </td>
        </tr>
    </table>
@endsection

@section('content')
    @php $fmt = fn ($n) => number_format((float) $n, 2); @endphp
    <table class="rpdf-table">
        <thead>
        <tr>
            <th>Category</th>
            <th class="text-end">Amount (৳)</th>
        </tr>
        </thead>
        <tbody>
        @foreach($datas as $categoryId => $categoryData)
            <tr>
                <td>{{ $categoryData['category_name'] }}</td>
                <td class="text-end fw-bold">৳ {{ $fmt($categoryData['total_amount']) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr class="rpdf-row-total">
            <td class="text-end"><strong>Grand Total</strong></td>
            <td class="text-end"><strong>৳ {{ $fmt($totalAmount ?? 0) }}</strong></td>
        </tr>
        </tfoot>
    </table>
@endsection
