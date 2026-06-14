@extends('backend.layouts.master')
@section('title')
    Day Wise Balance Report
@endsection
@push('styles')
    @include('backend.layouts.partials.report-styles')
@endpush
@section('admin-content')
    <div class="inv-page container-fluid py-3">
        @include('backend.layouts.partials.report-hero', [
            'reportTitle' => 'Day-wise Balance',
            'reportSubtitle' => 'Daily breakdown of collections, earnings, costs and net balance',
            'reportIcon' => 'fa-calendar-week',
            'resetRoute' => route('admin.reports.balance-day-wise'),
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
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Show report</button>
                </div>
            </form>

            <div class="p-4">
                <p class="text-muted small mb-3">
                    <i class="fas fa-info-circle"></i>
                    Each row is one day: <strong>Diagnostic collection</strong> + <strong>Hospital collection</strong> + <strong>Earn</strong> − <strong>Cost</strong> = <strong>Daily balance</strong>.
                </p>
                <div class="report-helper-output report-helper-wide">
                    {!! currentBalanceDayWise() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>.report-helper-wide > div, .report-helper-wide table { max-width: 100% !important; width: 100% !important; }</style>
@endpush
