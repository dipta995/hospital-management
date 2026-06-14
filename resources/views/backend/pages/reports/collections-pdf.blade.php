@extends('backend.layouts.report-pdf-layout')

@section('pdf-title')
    Diagnostic Collections Report
@endsection

@section('pdf-subtitle')
    Payments received against lab / diagnostic invoices
@endsection

@section('pdf-actions')
    <button onclick="window.print()" class="rpdf-btn">Print / Save as PDF</button>
@endsection

@section('pdf-summary')
    @php
        $fmt = fn ($n) => number_format((float) $n, 2);
        $due = ($overall_total_amount ?? 0) - ($overall_total_collection ?? 0);
    @endphp
    <table class="rpdf-kpi-table">
        <tr>
            <td>
                <div class="rpdf-kpi-label">Total Collected</div>
                <div class="rpdf-kpi-value success">৳ {{ $fmt($overall_total_collection ?? 0) }}</div>
            </td>
            <td>
                <div class="rpdf-kpi-label">Invoice (Net)</div>
                <div class="rpdf-kpi-value">৳ {{ $fmt($overall_total_amount ?? 0) }}</div>
            </td>
            <td>
                <div class="rpdf-kpi-label">Discount</div>
                <div class="rpdf-kpi-value warning">৳ {{ $fmt($overall_total_discount ?? 0) }}</div>
            </td>
            <td>
                <div class="rpdf-kpi-label">Due</div>
                <div class="rpdf-kpi-value danger">৳ {{ $fmt($due) }}</div>
            </td>
        </tr>
    </table>
@endsection

@section('content')
    @php $fmt = fn ($n) => number_format((float) $n, 2); @endphp
    <table class="rpdf-table">
        <tbody>
        @foreach($datas as $date => $invoices)
            <tr class="rpdf-row-date">
                <td colspan="3"><strong>{{ $date }}</strong></td>
                <td colspan="2" class="text-end"><strong>Sub ৳{{ $fmt($invoices->sum('total_amount') + $invoices->sum('total_discount')) }}</strong></td>
                <td class="text-end"><strong>Disc ৳{{ $fmt($invoices->sum('total_discount')) }}</strong></td>
                <td class="text-end"><strong>Col ৳{{ $fmt($invoices->sum('total_collection')) }}</strong></td>
            </tr>
            <tr>
                <th>#</th>
                <th>Tests</th>
                <th class="text-end">Sub Total</th>
                <th class="text-end">Discount</th>
                <th class="text-end">Collection</th>
                <th>Doctor</th>
                <th>Refer</th>
            </tr>

            @foreach($invoices as $invoice_id => $group)
                @php
                    $invoice = isset($group['data']) ? collect($group['data'])->first()->invoice ?? null : null;
                @endphp
                <tr class="rpdf-row-group">
                    <td colspan="7"><strong>Invoice: {{ $invoice?->invoice_number ?? 'N/A' }}</strong></td>
                </tr>

                @if(isset($group['data']))
                    @foreach($group['data'] as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                @foreach($invoice?->invoiceList ?? [] as $pr)
                                    {{ $pr->product?->name ?? '—' }}@if(!$loop->last), @endif
                                @endforeach
                            </td>
                            <td class="text-end">৳ {{ $fmt(($invoice?->total_amount ?? 0) + ($invoice?->discount_amount ?? 0)) }}</td>
                            <td class="text-end">৳ {{ $fmt($invoice?->discount_amount ?? 0) }}</td>
                            <td class="text-end fw-bold">৳ {{ $fmt($item->paid_amount) }}</td>
                            <td>{{ $invoice?->reeferDr?->name ?? '—' }}</td>
                            <td>{{ $invoice?->reeferBy?->name ?? '—' }}</td>
                        </tr>
                    @endforeach
                @endif
            @endforeach
        @endforeach
        </tbody>
    </table>
@endsection
