@extends('backend.layouts.report-pdf-layout')

@section('pdf-title')
    Referrer Commission Report
@endsection

@section('pdf-subtitle')
    Refer fees earned, paid and outstanding
@endsection

@section('pdf-period')
    {{ $startDate }} → {{ $endDate }}
@endsection

@section('pdf-summary')
    @php $fmt = fn ($n) => number_format((float) $n, 2); @endphp
    <table class="rpdf-kpi-table">
        <tr>
            <td>
                <div class="rpdf-kpi-label">Total Collection</div>
                <div class="rpdf-kpi-value">৳ {{ $fmt($toatalCollection ?? 0) }}</div>
            </td>
            <td>
                <div class="rpdf-kpi-label">Refer Fee</div>
                <div class="rpdf-kpi-value">৳ {{ $fmt($totalAmount ?? 0) }}</div>
            </td>
            <td>
                <div class="rpdf-kpi-label">Paid</div>
                <div class="rpdf-kpi-value success">৳ {{ $fmt($totalPaidAmount ?? 0) }}</div>
            </td>
            <td>
                <div class="rpdf-kpi-label">Due</div>
                <div class="rpdf-kpi-value danger">৳ {{ $fmt($totalDueAmount ?? 0) }}</div>
            </td>
        </tr>
    </table>
@endsection

@section('content')
    @php $fmt = fn ($n) => number_format((float) $n, 2); @endphp
    <table class="rpdf-table">
        <thead>
        <tr>
            <th>Invoice</th>
            <th>Refer By</th>
            <th>Doctor</th>
            <th>Patient</th>
            <th class="text-end">Refer Fee</th>
            <th class="text-end">Paid</th>
            <th class="text-end">Unpaid</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($datas as $item)
            @php
                $paid = $item->costs->sum('amount');
                $unpaid = $item->refer_fee_total - $paid;
                $editors = $item->costs
                    ->map(fn ($cost) => $cost->admin?->name)
                    ->filter()
                    ->unique()
                    ->implode(', ');
            @endphp
            <tr>
                <td>
                    <strong>{{ $item->invoice_number }}</strong>
                    <div class="text-muted">#{{ $item->id }} · {{ $item->creation_date }}</div>
                </td>
                <td>{{ $item->reeferBy->name ?? '—' }}</td>
                <td>{{ $item->reeferDr->name ?? '—' }}</td>
                <td>
                    {{ $item->patient_name }}
                    <div class="text-muted">Bill ৳{{ $fmt($item->total_amount + $item->discount_amount) }} · Disc ৳{{ $fmt($item->discount_amount) }}</div>
                </td>
                <td class="text-end fw-bold">৳ {{ $fmt($item->refer_fee_total) }}</td>
                <td class="text-end">৳ {{ $fmt($paid) }}</td>
                <td class="text-end">
                    ৳ {{ $fmt($unpaid) }}
                    @if($unpaid < 0)<span class="rpdf-badge rpdf-badge-extra">Extra</span>@endif
                </td>
                <td>
                    @if($unpaid > 0)
                        <span class="rpdf-badge rpdf-badge-unpaid">Unpaid</span>
                    @else
                        <span class="rpdf-badge rpdf-badge-paid">Paid</span>
                    @endif
                    @if($editors)
                        <div class="text-muted" style="margin-top:3px">By {{ $editors }}</div>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
