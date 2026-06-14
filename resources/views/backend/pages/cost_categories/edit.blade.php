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
            'formTitle' => 'Edit Cost Category',
            'formSubtitle' => $edited->name,
            'formIcon' => 'fa-tags',
        ])

        <div class="crud-card">
            @include('backend.layouts.partials.message')

            <form method="post" action="{{ route($pageHeader['update_route'], $edited->id) }}">
                @method('PUT')
                @csrf

                <div class="crud-form-section">
                    <div class="crud-form-section-header">
                        <i class="fas fa-info-circle"></i> Category Details
                    </div>
                    <div class="crud-form-section-body">
                        <div class="row crud-form-grid g-3">
                            <div class="col-md-8">
                                <x-default.label required="true" for="name">Category Name</x-default.label>
                                <x-default.input name="name" class="form-control" id="name" type="text" value="{{ old('name', $edited->name) }}"></x-default.input>
                                <x-default.input-error name="name"></x-default.input-error>
                            </div>
                            <div class="col-md-4">
                                <x-default.label for="type">Type</x-default.label>
                                @php $selectedType = old('type', $edited->type ?? 'diagnostic'); @endphp
                                <select name="type" id="type" class="form-select">
                                    <option value="diagnostic" @selected($selectedType === 'diagnostic')>Diagnostic</option>
                                    <option value="hospital" @selected($selectedType === 'hospital')>Hospital</option>
                                </select>
                                <x-default.input-error name="type"></x-default.input-error>
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
