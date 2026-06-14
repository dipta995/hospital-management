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
            'formTitle' => 'Create Referrer',
            'formSubtitle' => 'Add referring doctor and commission settings',
            'formIcon' => 'fa-user-md',
        ])

        <div class="crud-card">
            @include('backend.layouts.partials.message')

            <form method="post" action="{{ route($pageHeader['store_route']) }}">
                @csrf

                <div class="crud-form-section">
                    <div class="crud-form-section-header">
                        <i class="fas fa-user"></i> Personal Information
                    </div>
                    <div class="crud-form-section-body">
                        <div class="row crud-form-grid g-3">
                            <div class="col-md-6">
                                <x-default.label required="true" for="name">Name</x-default.label>
                                <x-default.input name="name" class="form-control" id="name" type="text" value="{{ old('name') }}"></x-default.input>
                                <x-default.input-error name="name"></x-default.input-error>
                            </div>
                            <div class="col-md-6">
                                <x-default.label required="true" for="type">Type</x-default.label>
                                <select class="form-select" name="type" id="type">
                                    <option value="">-- Choose Type --</option>
                                    @foreach(\App\Models\Reefer::$typeArray as $item)
                                        <option value="{{ $item }}" @selected(old('type') == $item)>{{ $item }}</option>
                                    @endforeach
                                </select>
                                <x-default.input-error name="type"></x-default.input-error>
                            </div>
                            <div class="col-md-6">
                                <label for="phone">Phone</label>
                                <input id="phone" class="form-control" name="phone" type="text" value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="office_time">Office Time</label>
                                <input id="office_time" class="form-control" name="office_time" type="time" value="{{ old('office_time') }}">
                                @error('office_time')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="designation">Designation</label>
                                <textarea id="designation" class="form-control" name="designation" rows="4">{{ old('designation') }}</textarea>
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
                                <label for="percent">Default Percent (%) <span class="text-danger">*</span></label>
                                <input id="percent" class="form-control" name="percent" type="number" step="0.01" value="{{ old('percent', 0) }}">
                                @error('percent')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-check mt-3 mb-2">
                            <input class="form-check-input" name="enable_custom_percent" value="yes" type="checkbox" id="enableCustomPercent"
                                {{ old('enable_custom_percent') === 'yes' ? 'checked' : '' }}>
                            <label class="form-check-label" for="enableCustomPercent">
                                Enable custom percent by category
                            </label>
                        </div>

                        <div id="customPercentFields" style="display: none;">
                            <div class="row crud-form-grid g-3">
                                @foreach($categories as $item)
                                    <div class="col-md-4">
                                        <label for="category_{{ $item->id }}">{{ $item->name }} (%)</label>
                                        <input id="category_{{ $item->id }}" class="form-control" step="0.01"
                                               name="custom_percent[{{ $item->id }}]" type="number"
                                               value="{{ old('custom_percent.' . $item->id, 0) }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="crud-form-actions">
                    <a href="{{ route($pageHeader['index_route']) }}" class="btn-crud-cancel">Cancel</a>
                    <button type="submit" class="btn btn-crud-submit">Create Referrer</button>
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
