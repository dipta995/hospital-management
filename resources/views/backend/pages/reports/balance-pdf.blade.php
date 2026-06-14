@extends('backend.layouts.report-pdf-layout')

@section('pdf-title')
    Monthly Balance Summary
@endsection

@section('pdf-subtitle')
    Collection + earnings minus costs for the selected period
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
    <div class="rpdf-balance-box">
        {!! currentBalanceMonth() !!}
    </div>
@endsection

@push('pdf-styles')
<style>
    .rpdf-balance-box > div {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        background: transparent !important;
        max-width: 100% !important;
    }
    .rpdf-balance-box p {
        display: table;
        width: 100%;
    }
    .rpdf-balance-box p span:last-child,
    .rpdf-balance-box p strong:last-child {
        float: right;
    }
</style>
@endpush
