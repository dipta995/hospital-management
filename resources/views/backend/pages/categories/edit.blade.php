@extends('backend.layouts.master')
@section('title')
    Edit {{ $pageHeader['title'] }}
@endsection

@push('styles')
    @include('backend.layouts.partials.crud-styles')
@endpush

@section('admin-content')
    <div class="crud-page container-fluid py-3">
        @include('backend.layouts.partials.crud-form-hero', [
            'formTitle' => 'Edit Category',
            'formSubtitle' => 'Update information for ' . $edited->name,
            'formIcon' => 'fa-layer-group',
        ])

        <div class="crud-card">
            @include('backend.layouts.partials.message')

            <form method="post" action="{{ route($pageHeader['update_route'], $edited->id) }}">
                @method('PUT')
                @csrf

                <div class="crud-form-section">
                    <div class="crud-form-section-header">
                        <i class="fas fa-info-circle"></i> Category Information
                    </div>
                    <div class="crud-form-section-body">
                        <div class="row crud-form-grid g-3">
                            <div class="col-md-4">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input id="name" class="form-control" name="name" type="text" value="{{ old('name', $edited->name) }}">
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="room_no">Room No <span class="text-danger">*</span></label>
                                <input id="room_no" class="form-control" name="room_no" type="text" value="{{ old('room_no', $edited->room_no) }}">
                                @error('room_no')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="room_name">Room Name <span class="text-danger">*</span></label>
                                <input id="room_name" class="form-control" name="room_name" type="text" value="{{ old('room_name', $edited->room_name) }}">
                                @error('room_name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="crud-form-actions">
                    <a href="{{ route($pageHeader['index_route']) }}" class="btn-crud-cancel">Cancel</a>
                    <button type="submit" class="btn btn-crud-submit">Update Category</button>
                </div>
            </form>
        </div>
    </div>
@endsection
