@extends('backend.layouts.master')
@section('title')
    Edit {{ $pageHeader['title'] }}
@endsection
@push('styles')

@endpush
@section('admin-content')
    <!-- partial -->
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Modify <strong>{{ $edited->name }}'s</strong> Information</h4>
                            @include('backend.layouts.partials.message')

                            <form class="cmxform" method="post"
                                  action="{{ route($pageHeader['update_route'], $edited->id) }}">
                                @method('PUT')
                                @csrf
                                <fieldset class="row">
                                    <div class="form-group col-md-6">
                                        <label for="name">Name <strong class="text-danger">*</strong></label>
                                        <input id="name" class="form-control "
                                               name="name" type="text" value="{{ old('name', $edited->name) }}">
                                        @error('name')
                                        <strong class="text-danger">{{ $errors->first('name') }}</strong>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <x-default.label required="true" for="type">Type</x-default.label>
                                        <select class="form-control" name="type" id="type">
                                            <option value="">--Choose--</option>
                                            @foreach(\App\Models\Reefer::$typeArray as $item)
                                                <option
                                                    @selected(old('type', $edited->type) == $item) value="{{ $item }}">{{ $item }}</option>
                                            @endforeach
                                        </select>
                                        <x-default.input-error name="type"></x-default.input-error>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="phone">Phone<strong
                                                class="text-danger">*</strong></label>
                                        <input id="phone"
                                               class="form-control @error('phone') is-invalid @enderror"
                                               name="phone" type="number"
                                               value="{{ old('phone', $edited->phone) }}">
                                        @error('phone')
                                        <strong class="text-danger">{{ $errors->first('phone') }}</strong>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="office_time">Time</label>
                                        <input id="office_time"
                                               class="form-control"
                                               name="office_time"
                                               type="time"
                                               value="{{ old('office_time', $edited->office_time ?? '') }}">
                                        @error('office_time')
                                        <strong class="text-danger">{{ $message }}</strong>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="percent">Percent</label>
                                        <input id="percent"
                                               class="form-control"
                                               name="percent" type="number"
                                               value="{{ old('percent', $edited->percent) }}">
                                        @error('percent')
                                        <strong class="text-danger">{{ $errors->first('percent') }}</strong>
                                        @enderror
                                    </div>
                                    {{-- Custom Percent per Category --}}
                                    <div class="form-group col-12">
                                        <div class="form-check mb-2">
                                            @php
                                                // Check if there is at least one custom percent set
                                                $hasCustomPercent = $edited->customParcent->isNotEmpty();
                                            @endphp

                                            <input class="form-check-input"
                                                   type="checkbox" value="yes"
                                                   id="enableCustomPercent"
                                                {{ $hasCustomPercent ? 'checked' : '' }}>
                                            <label class="form-check-label" for="enableCustomPercent">
                                                Enable Custom Percent by Category
                                            </label>
                                        </div>

                                        <div id="customPercentFields" style="{{ $hasCustomPercent ? '' : 'display: none;' }}">
                                            <h5>Custom Percent by Category (optional)</h5>
                                            <div class="row">
                                                @foreach($categories as $category)
                                                    @php
                                                        $custom = $edited->customParcent->firstWhere('category_id', $category->id) ?? null;
                                                        $percentValue = old("custom_percent.{$category->id}", $custom->percentage ?? null);
                                                    @endphp
                                                    <div class="col-md-6 mb-2">
                                                        <label>{{ $category->name }}</label>
                                                        <input type="number"
                                                               step="0.01"
                                                               name="custom_percent[{{ $category->id }}]"
                                                               class="form-control"
                                                               value="{{ $percentValue }}"
                                                            {{ $hasCustomPercent ? 'required' : '' }}>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>



                                    <div class="form-group col-md-12">
                                        <label for="designation">Designation</label>
                                        <textarea id="designation" class="form-control"
                                                  name="designation">{{ old('designation', $edited->designation) }}</textarea>
                                        @error('designation')
                                        <strong class="text-danger">{{ $errors->first('designation') }}</strong>
                                        @enderror
                                    </div>
                                    <x-default.button class="float-end mt-2 btn-success">Update</x-default.button>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->

        <!-- partial -->
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
                    $('#customPercentFields input')
                        .val('')               // clear value
                        .attr('required', false);
                }
            });
        });
    </script>
    <script>
        $('#designation').summernote({
            tabsize: 2,
            height: 400,
        })
    </script>
@endpush
