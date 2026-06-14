@extends('backend.layouts.report-pdf-layout')

@section('pdf-title')
    Hospital Collections Report
@endsection

@section('pdf-subtitle')
    Payments received against hospital / admit invoices
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
        @foreach($datas as $date => $admits)
            <tr class="rpdf-row-date">
                <td colspan="3"><strong>{{ $date }}</strong></td>
                <td colspan="2" class="text-end"><strong>Sub ৳{{ $fmt($admits->sum('total_amount') + $admits->sum('total_discount')) }}</strong></td>
                <td class="text-end"><strong>Disc ৳{{ $fmt($admits->sum('total_discount')) }}</strong></td>
                <td class="text-end"><strong>Col ৳{{ $fmt($admits->sum('total_collection')) }}</strong></td>
                <td></td>
            </tr>
            <tr>
                <th>#</th>
                <th>Admit ID</th>
                <th>Services</th>
                <th class="text-end">Sub Total</th>
                <th class="text-end">Discount</th>
                <th class="text-end">Collection</th>
                <th>Doctor</th>
                <th>Refer</th>
            </tr>

            @foreach($admits as $admitId => $group)
                @php
                    $firstPayment = isset($group['data']) ? collect($group['data'])->first() : null;
                    $admit = $firstPayment?->admit;
                @endphp
                <tr class="rpdf-row-group">
                    <td colspan="8"><strong>Admit: {{ $admit->id ?? 'N/A' }}</strong></td>
                </tr>

                @if(isset($group['data']))
                    @foreach($group['data'] as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $admit->id ?? 'N/A' }}</td>
                            <td>
                                @foreach(($admit?->recepts ?? []) as $recept)
                                    @foreach($recept->receptList ?? [] as $pr)
                                        {{ $pr->service->name ?? '—' }}@if(!$loop->last), @endif
                                    @endforeach
                                @endforeach
                            </td>
                            <td class="text-end">৳ {{ $fmt(($group['total_amount'] ?? 0) + ($group['total_discount'] ?? 0)) }}</td>
                            <td class="text-end">৳ {{ $fmt($group['total_discount'] ?? 0) }}</td>
                            <td class="text-end fw-bold">৳ {{ $fmt($item->paid_amount) }}</td>
                            <td>{{ $admit?->drreefer?->name ?? '—' }}</td>
                            <td>{{ $admit?->reefer?->name ?? '—' }}</td>
                        </tr>
                    @endforeach
                @endif
            @endforeach
        @endforeach
        </tbody>
    </table>
@endsection
