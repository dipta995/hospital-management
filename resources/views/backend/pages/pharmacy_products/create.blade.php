@extends('backend.layouts.master')
@section('title')
    Create {{ $pageHeader['title'] }}
@endsection

@push('styles')
    @include('backend.layouts.partials.crud-styles')
    @include('backend.layouts.partials.pharmacy-styles')
    @include('backend.layouts.partials.cost-category-select2-assets')
@endpush

@section('admin-content')
    <div class="crud-page pharm-page container-fluid py-3">
        @include('backend.layouts.partials.crud-form-hero', [
            'formTitle' => 'Add Pharmacy Product',
            'formSubtitle' => 'Medicine details, pricing & stock alert',
            'formIcon' => 'fa-pills',
        ])

        <div class="crud-card">
            @include('backend.layouts.partials.message')

            <form action="{{ route('admin.pharmacy_products.store') }}" method="POST">
                @csrf

                <div class="crud-form-section">
                    <div class="crud-form-section-header"><i class="fas fa-tags"></i> Classification</div>
                    <div class="crud-form-section-body">
                        <div class="row crud-form-grid g-3">
                            <div class="col-md-3">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select pharm-select" required>
                                    <option value="">Select</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Brand <span class="text-danger">*</span></label>
                                <select name="brand_id" class="form-select pharm-select" required>
                                    <option value="">Select</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" @selected(old('brand_id') == $brand->id)>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Type <span class="text-danger">*</span></label>
                                <select name="type_id" class="form-select pharm-select" required>
                                    <option value="">Select</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type->id }}" @selected(old('type_id') == $type->id)>{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Unit <span class="text-danger">*</span></label>
                                <select name="quantity_type_id" class="form-select pharm-select" required>
                                    <option value="">Select</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}" @selected(old('quantity_type_id') == $unit->id)>{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="crud-form-section">
                    <div class="crud-form-section-header"><i class="fas fa-capsules"></i> Product Details</div>
                    <div class="crud-form-section-body">
                        <div class="row crud-form-grid g-3">
                            <div class="col-md-4">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Generic Name</label>
                                <input type="text" name="generic_name" value="{{ old('generic_name') }}" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Strength</label>
                                <input type="text" name="strength" value="{{ old('strength') }}" class="form-control" placeholder="500mg">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Barcode</label>
                                <input type="text" name="barcode" value="{{ old('barcode') }}" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Purchase Price <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price', 0) }}" min="0" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Sell Price <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="sell_price" value="{{ old('sell_price', 0) }}" min="0" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Alert Qty <span class="text-danger">*</span></label>
                                <input type="number" name="alert_qty" value="{{ old('alert_qty', 10) }}" min="0" class="form-control" required>
                            </div>
                            @if(\Illuminate\Support\Facades\Schema::hasColumn('pharmacy_products', 'status'))
                                <div class="col-md-3 d-flex align-items-end">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="status" id="status" value="1" checked>
                                        <label class="form-check-label" for="status">Active for sales</label>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="crud-form-actions">
                    <a href="{{ route('admin.pharmacy_products.index') }}" class="btn-crud-cancel">Cancel</a>
                    <button type="submit" class="btn btn-crud-submit">Save Product</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('.pharm-select').select2({ width: '100%', minimumResultsForSearch: 0 });
    });
</script>
@endpush
