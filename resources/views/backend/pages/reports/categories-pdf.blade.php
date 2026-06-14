@extends('backend.layouts.report-pdf-layout')

@section('pdf-title')
    Sales by Category Report
@endsection

@section('pdf-subtitle')
    Diagnostic tests / products grouped by category
@endsection

@section('pdf-period')
    @if(request('start_date') || request('end_date'))
        {{ request('start_date') ?: '…' }} → {{ request('end_date') ?: '…' }}
    @else
        All dates
    @endif
@endsection

@section('content')
    @php
        $fmt = fn ($n) => number_format((float) $n, 2);
        $grandTotal = 0;
        foreach ($datas as $cat) {
            $grandTotal += ($cat['total_price'] ?? 0) - ($cat['discount_price'] ?? 0);
        }
    @endphp

    <table class="rpdf-kpi-table">
        <tr>
            <td colspan="2">
                <div class="rpdf-kpi-label">Categories</div>
                <div class="rpdf-kpi-value">{{ count($datas) }}</div>
            </td>
            <td colspan="2">
                <div class="rpdf-kpi-label">Net Total (after discount)</div>
                <div class="rpdf-kpi-value">৳ {{ $fmt($grandTotal) }}</div>
            </td>
        </tr>
    </table>

    <table class="rpdf-table">
        <thead>
        <tr>
            <th>Category</th>
            <th>Product / Test</th>
            <th>Invoice</th>
            <th>Doctor</th>
            <th class="text-end">After Discount</th>
            <th>Date</th>
            <th class="text-end">List Price</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($datas as $categoryName => $categoryData)
            <tr class="rpdf-row-date">
                <td colspan="7">
                    <strong>{{ $categoryName }}</strong>
                    <span class="text-muted">({{ $categoryData['total_count'] ?? 0 }} items)</span>
                </td>
            </tr>
            @foreach ($categoryData['invoices'] as $invoiceList)
                <tr>
                    <td></td>
                    <td>{{ $invoiceList->product?->name ?? '—' }}</td>
                    <td>
                        {{ $invoiceList->invoice?->invoice_number ?? '—' }}
                        <span class="text-muted">({{ $invoiceList->invoice?->patient_no ?? '—' }})</span>
                    </td>
                    <td>{{ $invoiceList->invoice?->reeferDr?->name ?? '—' }}</td>
                    <td class="text-end fw-bold">৳ {{ $fmt($invoiceList->price - $invoiceList->discount_price) }}</td>
                    <td>{{ \Carbon\Carbon::parse($invoiceList->created_at)->format('d M Y') }}</td>
                    <td class="text-end">৳ {{ $fmt($invoiceList->price) }}</td>
                </tr>
            @endforeach
            <tr class="rpdf-row-subtotal">
                <td colspan="4" class="text-end"><strong>Category subtotal</strong></td>
                <td class="text-end"><strong>৳ {{ $fmt($categoryData['total_price'] - $categoryData['discount_price']) }}</strong></td>
                <td colspan="2"></td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
