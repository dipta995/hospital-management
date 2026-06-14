@extends('backend.layouts.master')
@section('title')
    Edit {{ $pageHeader['title'] }}
@endsection
@push('styles')
    @include('backend.layouts.partials.crud-styles')
    @include('backend.layouts.partials.pharmacy-styles')
    @include('backend.layouts.partials.cost-category-select2-assets')
@endpush
@section('admin-content')
<div class="crud-page pharm-page container-fluid py-3">
    @include('backend.layouts.partials.crud-form-hero', [
        'formTitle' => 'Edit Pharmacy Purchase',
        'formSubtitle' => 'Purchase #' . $purchase->id . ' · ' . optional($purchase->supplier)->name,
        'formIcon' => 'fa-truck-loading',
    ])

    @include('backend.layouts.partials.message')
    @include('backend.layouts.partials.pharmacy-purchase-form')
</div>
@endsection

@push('scripts')
    @include('backend.layouts.partials.pharmacy-purchase-form-scripts', [
        'submitUrl' => route('admin.pharmacy_purchases.update', $purchase->id),
        'submitMethod' => 'PUT',
        'redirectUrl' => route('admin.pharmacy_purchases.index'),
        'submitLabel' => 'Update Purchase',
    ])
@endpush
