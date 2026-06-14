@extends('backend.layouts.master')
@section('title')
    Edit {{ $pageHeader['title'] }}
@endsection
@push('styles')
    @include('backend.layouts.partials.crud-styles')
    @include('backend.layouts.partials.pharmacy-styles')
    @include('backend.layouts.partials.cost-category-select2-assets')
    <style>
        .purchase-page .crud-hero { background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%); color: #fff; }
        .purchase-page .pharm-pos-panel-head { background: #f0fdfa; color: #0f766e; border-color: #ccfbf1; }
        .purchase-page .pharm-summary-row.total { color: #0f766e; }
        .purchase-page .btn-crud-submit { background: linear-gradient(135deg, #0f766e, #14b8a6); }
        .purchase-page .line-total.text-primary { color: #0f766e !important; }
    </style>
@endpush
@section('admin-content')
<div class="crud-page purchase-page container-fluid py-3">
    @include('backend.layouts.partials.crud-form-hero', [
        'formTitle' => 'Edit Purchase',
        'formSubtitle' => 'Purchase #' . $purchase->id . ' · ' . optional($purchase->supplier)->name,
        'formIcon' => 'fa-shopping-cart',
    ])

    @include('backend.layouts.partials.message')
    @include('backend.layouts.partials.inventory-purchase-form', [
        'purchase' => $purchase,
        'purchaseItems' => $purchaseItems,
    ])
</div>
@endsection

@push('scripts')
    @include('backend.layouts.partials.inventory-purchase-form-scripts', [
        'submitUrl' => route('admin.purchases.update', $purchase->id),
        'submitMethod' => 'PUT',
        'redirectUrl' => route('admin.purchases.index'),
        'submitLabel' => 'Update Purchase',
        'isEdit' => true,
    ])
@endpush
