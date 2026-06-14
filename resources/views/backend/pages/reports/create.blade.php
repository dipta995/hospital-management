@extends('backend.layouts.master')
@section('title')
    Reports
@endsection

@push('styles')
    @include('backend.layouts.partials.crud-styles')
    @include('backend.layouts.partials.report-styles')
@endpush

@section('admin-content')
    <div class="crud-page inv-page container-fluid py-3">
        @include('backend.layouts.partials.report-hero', [
            'heroTitle' => 'Reports Hub',
            'heroSubtitle' => 'Use the sidebar Reports section for collections, balance, references, and more.',
            'heroIcon' => 'fa-chart-bar',
        ])

        <div class="inv-panel">
            <div class="p-4">
                <p class="text-muted mb-3">This page is a placeholder. Open reports from the sidebar:</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.reports.collections') }}" class="btn btn-primary btn-sm">Collections</a>
                    <a href="{{ route('admin.reports.balance') }}" class="btn btn-outline-primary btn-sm">Balance</a>
                    <a href="{{ route('admin.reports.references') }}" class="btn btn-outline-primary btn-sm">References</a>
                    <a href="{{ route('admin.reports.costs') }}" class="btn btn-outline-primary btn-sm">Costs</a>
                    <a href="{{ route('admin.reports.pharmacy-stock') }}" class="btn btn-outline-primary btn-sm">Pharmacy Stock</a>
                </div>
            </div>
        </div>
    </div>
@endsection
