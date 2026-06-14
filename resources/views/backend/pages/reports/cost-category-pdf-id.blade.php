@extends('backend.layouts.report-pdf-layout')

@section('pdf-title')
    Cost Report — Single Category
@endsection

@section('pdf-subtitle')
    Line-by-line expenses for one category
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
            <th>Reason / Details</th>
            <th>Payment Type</th>
            <th>Date</th>
            <th class="text-end">Amount (৳)</th>
        </tr>
        </thead>
        <tbody>
        @foreach($datas as $item)
            <tr>
                <td>
                    {{ $item->reason ?? '—' }}
                    @if(!empty($item->invoice->invoice_number))
                        <span class="text-muted">({{ $item->invoice->invoice_number }})</span>
                    @endif
                </td>
                <td>{{ $item->payment_type ?? '—' }}</td>
                <td>{{ $item->creation_date }}</td>
                <td class="text-end fw-bold">৳ {{ $fmt($item->amount) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr class="rpdf-row-total">
            <td colspan="3" class="text-end"><strong>Grand Total</strong></td>
            <td class="text-end"><strong>৳ {{ $fmt($totalAmount ?? 0) }}</strong></td>
        </tr>
        </tfoot>
    </table>
@endsection
