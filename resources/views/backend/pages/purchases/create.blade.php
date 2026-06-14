@extends('backend.layouts.master')
@section('title')
    Create {{ $pageHeader['title'] }}
@endsection
@push('styles')
    @include('backend.layouts.partials.crud-styles')
    @include('backend.layouts.partials.pharmacy-styles')
    @include('backend.layouts.partials.cost-category-select2-assets')
    <style>
        .purchase-page .crud-hero,
        .purchase-page .pharm-pos-panel-head { background: #f0fdfa; color: #0f766e; }
        .purchase-page .crud-hero { background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%); color: #fff; }
        .purchase-page .pharm-pos-panel-head { border-color: #ccfbf1; }
        .purchase-page .pharm-summary-row.total { color: #0f766e; }
        .purchase-page .btn-crud-submit { background: linear-gradient(135deg, #0f766e, #14b8a6); }
        .purchase-page .line-total.text-primary { color: #0f766e !important; }
    </style>
@endpush
@section('admin-content')
<div class="crud-page purchase-page container-fluid py-3">
    @include('backend.layouts.partials.crud-form-hero', [
        'formTitle' => 'New Inventory Purchase',
        'formSubtitle' => 'Add stock from supplier — totals auto-calculate',
        'formIcon' => 'fa-shopping-cart',
    ])

    @include('backend.layouts.partials.message')
    @include('backend.layouts.partials.inventory-purchase-form', [
        'purchase' => null,
        'purchaseItems' => collect(),
    ])
</div>
@endsection

@push('scripts')
    @include('backend.layouts.partials.inventory-purchase-form-scripts', [
        'submitUrl' => route('admin.purchases.store'),
        'submitMethod' => 'POST',
        'redirectUrl' => route('admin.purchases.index'),
        'submitLabel' => 'Save Purchase',
        'isEdit' => false,
    ])
@endpush
