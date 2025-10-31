@extends('backend.layouts.master')
@section('title')
    Create New {{ $pageHeader['title'] }}
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
                            <h4 class="card-title">Create New {{ $pageHeader['title'] }}</h4>
                            @include('backend.layouts.partials.message')
                            <form class="cmxform" method="post" action="{{ route($pageHeader['store_route']) }}">
                                @csrf
                                <fieldset class="row">
                                    <div class="form-group col-md-6">
                                        <x-default.label required="true" for="name">Name <strong
                                                class="text-danger">*</strong></x-default.label>
                                        <x-default.input name="name" class="form-control" id="name"
                                                         type="text"></x-default.input>
                                        <x-default.input-error name="name"></x-default.input-error>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <x-default.label required="true" for="type">Type</x-default.label>
                                        <select class="form-control" name="type" id="type">
                                            <option value="">--Choose--</option>
                                            @foreach(\App\Models\Reefer::$typeArray as $item)
                                                <option value="{{ $item }}">{{ $item }}</option>
                                            @endforeach
                                        </select>
                                        <x-default.input-error name="type"></x-default.input-error>
                                    </div>


                                    <div class="form-group col-md-6">
                                        <label for="phone">Phone</label>
                                        <input id="phone"
                                               class="form-control"
                                               name="phone" type="text" value="{{ old('phone') }}">
                                        @error('phone')
                                        <strong class="text-danger">{{ $errors->first('phone') }}</strong>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="office_time">Time</label>
                                        <input id="office_time"
                                               class="form-control"
                                               name="office_time" type="time" value="{{ old('office_time') }}">
                                        @error('office_time')
                                        <strong class="text-danger">{{ $errors->first('office_time') }}</strong>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="percent">Percent (%) <strong class="text-danger">*</strong></label>
                                        <input id="percent"
                                               class="form-control"
                                               name="percent" type="number" value="{{ old('percent', 0) }}">
                                        @error('percent')
                                        <strong class="text-danger">{{ $errors->first('percent') }}</strong>
                                        @enderror
                                    </div>
                                    {{-- Category-specific Percentages --}}
                                    <div class="col-md-12 mt-4">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" value="yes"   type="checkbox" id="enableCustomPercent">
                                            <label class="form-check-label" for="enableCustomPercent">
                                                Enable Custom Percent by Category
                                            </label>
                                        </div>

                                        <div id="customPercentFields" style="display: none;">
                                        <h5>Custom Percent by Category (optional)</h5>
                                        <div class="row">
                                            @foreach($categories as $item)
                                                <div class="form-group col-md-4">
                                                    <label for="category_{{ $item->id }}">{{ $item->name }} (%)</label>
                                                    <input
                                                        id="category_{{ $item->id }}"
                                                        class="form-control"
                                                        step="0.01"
                                                        name="custom_percent[{{ $item->id }}]"
                                                        type="number" required
                                                        value="{{ old('custom_percent.' . $item->id,0) }}">
                                                </div>
                                            @endforeach
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="designation">Designation</label>
                                        <textarea id="designation" class="form-control"
                                                  name="designation">{{ old('designation') }}</textarea>
                                        @error('designation')
                                        <strong class="text-danger">{{ $errors->first('designation') }}</strong>
                                        @enderror
                                    </div>
                                    <x-default.button class="float-end mt-2 btn-success">Create</x-default.button>

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
