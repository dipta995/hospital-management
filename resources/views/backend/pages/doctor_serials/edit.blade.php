@extends('backend.layouts.master')

@section('title')
    Edit {{ $pageHeader['title'] }}
@endsection

@push('styles')

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
@endpush

@section('admin-content')

    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Modify <strong>{{ $edited->patient_name }}'s</strong> Information</h4>
                            @include('backend.layouts.partials.message')

                            <form class="cmxform" method="post" action="{{ route($pageHeader['update_route'], $edited->id) }}">
                                @method('PUT')
                                @csrf
                                <fieldset>
                                    <div class="row">
{{--                                        <!-- Doctor Name -->--}}
{{--                                        <div class="form-group col-md-6">--}}
{{--                                            <x-default.label required="true" for="reefer_id">Doctor Name</x-default.label>--}}
{{--                                            <select class="form-control" name="reefer_id" id="reefer_id">--}}
{{--                                                <option value="">--Choose--</option>--}}
{{--                                                @foreach($reefers as $item)--}}
{{--                                                    <option value="{{ $item->id }}">{{ $item->name }}</option>--}}
{{--                                                @endforeach--}}
{{--                                            </select>--}}
{{--                                            <x-default.input-error name="reefer_id" />--}}
{{--                                        </div>--}}

                                        <!-- Patient Name -->
                                        <div class="form-group col-md-6">
                                            <x-default.label required="true" for="patient_name">Patient Name</x-default.label>
                                            <x-default.input name="patient_name" class="form-control" value="{{ old('patient_name',$edited->patient_name) }}" id="patient_name" type="text" />
                                            <x-default.input-error name="patient_name" />
                                        </div>

                                        <!-- Patient Phone -->
                                        <div class="form-group col-md-6">
                                            <x-default.label for="patient_phone">Patient Phone Number</x-default.label>
                                            <x-default.input name="patient_phone" value="{{ old('patient_phone',$edited->patient_phone) }}"  class="form-control" id="patient_phone" type="text" placeholder="Enter phone number" />
                                            <x-default.input-error name="patient_phone" />
                                        </div>
                                        <!-- Patient Phone -->
                                        <div class="form-group col-md-6">
                                            <x-default.label for="patient_age_year">Patient Age</x-default.label>
                                            <x-default.input name="patient_age_year" class="form-control" value="{{ old('patient_age_year',$edited->patient_age_year) }}"
                                                             id="patient_age_year" type="text"
                                                             placeholder="Enter Age"/>
                                            <x-default.input-error name="patient_age_year"/>
                                        </div>

                                        <!-- Patient Phone -->
                                        <div class="form-group col-md-6">
                                            <x-default.label for="serial_number">Serial No</x-default.label>
                                            <x-default.input name="serial_number" class="form-control" value="{{ old('serial_number',$edited->serial_number) }}"  id="serial_number" type="text" placeholder="Enter Serial number" />
                                            <x-default.input-error name="serial_number" />
                                        </div>

                                        <!-- Date -->
                                        <div class="form-group col-md-6">
                                            <x-default.label for="date">Date <strong class="text-danger">*</strong></x-default.label>
                                            <input id="date"
                                                   class="form-control"
                                                   name="date"
                                                   type="date"
                                                   value="{{ \Carbon\Carbon::parse($edited->date)->format('Y-m-d') }}"
                                                   min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                            @error('date')
                                            <strong class="text-danger">{{ $message }}</strong>
                                            @enderror
                                        </div>

                                        <!-- Remarks -->
                                        <div class="form-group col-md-6">
                                            <x-default.label for="remarks">Remarks</x-default.label>
                                            <x-default.input name="remarks" value="{{ old('remarks',$edited->remarks) }}"  class="form-control" id="remarks" type="text" placeholder="Enter Remarks" />
                                            <x-default.input-error name="remarks" />
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end mt-3">
                                        <x-default.button class="btn-success">Create</x-default.button>
                                    </div>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize select2 for searchable dropdown
            $('#reefer_id').select2({
                placeholder: "Select a Doctor",
                allowClear: true
            });
        });
    </script>
@endpush
