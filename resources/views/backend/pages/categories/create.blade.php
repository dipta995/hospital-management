@extends('backend.layouts.master')
@section('title')
    Create {{ $pageHeader['title'] }}
@endsection

@push('styles')
    @include('backend.layouts.partials.crud-styles')
@endpush

@section('admin-content')
    <div class="crud-page container-fluid py-3">
        @include('backend.layouts.partials.crud-form-hero', [
            'formTitle' => 'Create Category',
            'formSubtitle' => 'Add a new lab test category',
            'formIcon' => 'fa-layer-group',
        ])

        <div class="crud-card">
            @include('backend.layouts.partials.message')

            <form method="post" action="{{ route($pageHeader['store_route']) }}">
                @csrf

                <div class="crud-form-section">
                    <div class="crud-form-section-header">
                        <i class="fas fa-info-circle"></i> Category Information
                    </div>
                    <div class="crud-form-section-body">
                        <div class="row crud-form-grid g-3">
                            <div class="col-md-4">
                                <x-default.label required="true" for="name">Name</x-default.label>
                                <x-default.input name="name" class="form-control" id="name" type="text" value="{{ old('name') }}"></x-default.input>
                                <x-default.input-error name="name"></x-default.input-error>
                            </div>
                            <div class="col-md-4">
                                <x-default.label required="true" for="room_no">Room No</x-default.label>
                                <x-default.input name="room_no" class="form-control" id="room_no" type="text" value="{{ old('room_no') }}"></x-default.input>
                                <x-default.input-error name="room_no"></x-default.input-error>
                            </div>
                            <div class="col-md-4">
                                <x-default.label required="true" for="room_name">Room Name</x-default.label>
                                <x-default.input name="room_name" class="form-control" id="room_name" type="text" value="{{ old('room_name') }}"></x-default.input>
                                <x-default.input-error name="room_name"></x-default.input-error>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="crud-form-actions">
                    <a href="{{ route($pageHeader['index_route']) }}" class="btn-crud-cancel">Cancel</a>
                    <button type="submit" class="btn btn-crud-submit">Create Category</button>
                </div>
            </form>
        </div>
    </div>
@endsection
