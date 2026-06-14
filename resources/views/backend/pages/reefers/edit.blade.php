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
            'formTitle' => 'Edit Referrer',
            'formSubtitle' => 'Update ' . $edited->name,
            'formIcon' => 'fa-user-md',
        ])

        <div class="crud-card">
            @include('backend.layouts.partials.message')

            <form method="post" action="{{ route($pageHeader['update_route'], $edited->id) }}">
                @method('PUT')
                @csrf

                <div class="crud-form-section">
                    <div class="crud-form-section-header">
                        <i class="fas fa-user"></i> Personal Information
                    </div>
                    <div class="crud-form-section-body">
                        <div class="row crud-form-grid g-3">
                            <div class="col-md-6">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input id="name" class="form-control" name="name" type="text" value="{{ old('name', $edited->name) }}">
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <x-default.label required="true" for="type">Type</x-default.label>
                                <select class="form-select" name="type" id="type">
                                    <option value="">-- Choose Type --</option>
                                    @foreach(\App\Models\Reefer::$typeArray as $item)
                                        <option @selected(old('type', $edited->type) == $item) value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                                <x-default.input-error name="type"></x-default.input-error>
                            </div>
                            <div class="col-md-6">
                                <label for="phone">Phone</label>
                                <input id="phone" class="form-control" name="phone" type="text" value="{{ old('phone', $edited->phone) }}">
                                @error('phone')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="office_time">Office Time</label>
                                <input id="office_time" class="form-control" name="office_time" type="time"
                                       value="{{ old('office_time', $edited->office_time ?? '') }}">
                                @error('office_time')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="designation">Designation</label>
                                <textarea id="designation" class="form-control" name="designation" rows="4">{{ old('designation', $edited->designation) }}</textarea>
                                @error('designation')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="crud-form-section">
                    <div class="crud-form-section-header">
                        <i class="fas fa-percent"></i> Commission Settings
                    </div>
                    <div class="crud-form-section-body">
                        <div class="row crud-form-grid g-3">
                            <div class="col-md-4">
                                <label for="percent">Default Percent (%)</label>
                                <input id="percent" class="form-control" name="percent" type="number" step="0.01"
                                       value="{{ old('percent', $edited->percent) }}">
                                @error('percent')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        @php
                            $hasCustomPercent = $edited->customParcent->isNotEmpty();
                        @endphp

                        <div class="form-check mt-3 mb-2">
                            <input class="form-check-input" type="checkbox" value="yes" id="enableCustomPercent"
                                {{ $hasCustomPercent ? 'checked' : '' }}>
                            <label class="form-check-label" for="enableCustomPercent">
                                Enable custom percent by category
                            </label>
                        </div>

                        <div id="customPercentFields" style="{{ $hasCustomPercent ? '' : 'display: none;' }}">
                            <div class="row crud-form-grid g-3">
                                @foreach($categories as $category)
                                    @php
                                        $custom = $edited->customParcent->firstWhere('category_id', $category->id) ?? null;
                                        $percentValue = old("custom_percent.{$category->id}", $custom->percentage ?? null);
                                    @endphp
                                    <div class="col-md-4">
                                        <label>{{ $category->name }} (%)</label>
                                        <input type="number" step="0.01" name="custom_percent[{{ $category->id }}]"
                                               class="form-control" value="{{ $percentValue }}"
                                            {{ $hasCustomPercent ? 'required' : '' }}>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="crud-form-actions">
                    <a href="{{ route($pageHeader['index_route']) }}" class="btn-crud-cancel">Cancel</a>
                    <button type="submit" class="btn btn-crud-submit">Update Referrer</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#enableCustomPercent').on('change', function () {
                if ($(this).is(':checked')) {
                    $('#customPercentFields').slideDown();
                    $('#customPercentFields input').attr('required', true);
                } else {
                    $('#customPercentFields').slideUp();
                    $('#customPercentFields input').val('').attr('required', false);
                }
            });

            $('#designation').summernote({
                tabsize: 2,
                height: 280,
            });
        });
    </script>
@endpush
