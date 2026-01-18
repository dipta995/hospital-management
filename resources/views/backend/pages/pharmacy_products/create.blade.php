@extends('backend.layouts.master')
@section('title')
    Create New {{ $pageHeader['title'] }}
@endsection
@push('styles')

@endpush
@section('admin-content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">Create Pharmacy Product</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.pharmacy_products.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-control" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Brand <span class="text-danger">*</span></label>
                        <select name="brand_id" class="form-control" required>
                            <option value="">Select Brand</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('brand_id')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type_id" class="form-control" required>
                            <option value="">Select Type</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}" {{ old('type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('type_id')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Quantity Unit <span class="text-danger">*</span></label>
                        <select name="quantity_type_id" class="form-control" required>
                            <option value="">Select Unit</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ old('quantity_type_id') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('quantity_type_id')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                        @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Generic Name</label>
                        <input type="text" name="generic_name" value="{{ old('generic_name') }}" class="form-control">
                        @error('generic_name')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Strength</label>
                        <input type="text" name="strength" value="{{ old('strength') }}" class="form-control">
                        @error('strength')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Barcode</label>
                        <input type="text" name="barcode" value="{{ old('barcode') }}" class="form-control">
                        @error('barcode')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Alert Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="alert_qty" value="{{ old('alert_qty', 0) }}" min="0" class="form-control" required>
                        @error('alert_qty')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Purchase Price <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price', 0) }}" min="0" class="form-control" required>
                        @error('purchase_price')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Sell Price <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="sell_price" value="{{ old('sell_price', 0) }}" min="0" class="form-control" required>
                        @error('sell_price')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-control" required>
                            <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="{{ route('admin.pharmacy_products.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
