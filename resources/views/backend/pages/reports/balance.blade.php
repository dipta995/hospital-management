@extends('backend.layouts.master')
@section('title')
    {{ $pageHeader['title'] }}
@endsection
@push('styles')
    @include('backend.layouts.partials.report-styles')
@endpush
@section('admin-content')
    @php $fmt = fn ($n) => number_format((float) $n, 2); @endphp

    <div class="inv-page container-fluid py-3">
        @include('backend.layouts.partials.report-hero', [
            'reportTitle' => 'Monthly Balance Summary',
            'reportSubtitle' => 'Collection + earnings minus costs for the selected period',
            'reportIcon' => 'fa-balance-scale',
            'resetRoute' => route('admin.reports.balance'),
        ])

        @include('backend.layouts.partials.message')

        <div class="inv-panel mb-3">
            <form method="GET" action="" class="inv-filter-toolbar">
                <div class="filter-field">
                    <label class="form-label">From date</label>
                    <input type="date" class="form-control" name="from_date" value="{{ request('from_date') }}">
                </div>
                <div class="filter-field">
                    <label class="form-label">To date</label>
                    <input type="date" class="form-control" name="to_date" value="{{ request('to_date') }}">
                </div>
                <div class="filter-field" style="max-width:120px">
                    <label class="form-label">PDF</label>
                    <select name="pdf" class="form-select">
                        <option value="no" @selected(request('pdf') !== 'yes')>No</option>
                        <option value="yes" @selected(request('pdf') === 'yes')>Export</option>
                    </select>
                </div>
                <div class="inv-filter-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Calculate</button>
                </div>
            </form>

            <div class="p-4">
                <p class="text-muted small mb-3">
                    <i class="fas fa-info-circle"></i>
                    <strong>How to read:</strong> Diagnostic + Hospital collections and other earnings are added together, then costs are subtracted to show your <strong>Current Balance</strong>.
                    Leave dates empty to use the current month.
                </p>
                <div class="report-helper-output">
                    {!! currentBalanceMonth() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
