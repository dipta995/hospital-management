@extends('backend.layouts.report-pdf-layout')

@section('pdf-title')
    Cost Report — By Date
@endsection

@section('pdf-subtitle')
    Expenses grouped by date and category
@endsection

@section('pdf-period')
    {{ request('start_date') ?? 'Today' }} → {{ request('end_date') ?? 'Today' }}
    · {{ ucfirst(request('type', 'diagnostic')) }}
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
            <th>Date</th>
            <th>Category</th>
            <th>Details / Reason</th>
            <th class="text-end">Amount (৳)</th>
        </tr>
        </thead>
        <tbody>
        @foreach($datas as $date => $dateData)
            <tr class="rpdf-row-date">
                <td colspan="4">
                    <strong>{{ $date }}</strong>
                    — Day total: ৳ {{ $fmt($dateData['total_per_date']) }}
                </td>
            </tr>

            @foreach($dateData['categories'] as $category)
                <tr class="rpdf-row-group">
                    <td></td>
                    <td colspan="3"><strong>{{ $category['category_name'] }}</strong></td>
                </tr>
                @foreach($category['data'] as $cost)
                    <tr>
                        <td></td>
                        <td></td>
                        <td>{{ $cost->reason ?? '—' }}</td>
                        <td class="text-end">৳ {{ $fmt($cost->amount) }}</td>
                    </tr>
                @endforeach
                <tr class="rpdf-row-subtotal">
                    <td colspan="3" class="text-end"><strong>Category subtotal</strong></td>
                    <td class="text-end"><strong>৳ {{ $fmt($category['total_amount']) }}</strong></td>
                </tr>
            @endforeach
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
