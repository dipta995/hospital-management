@extends('backend.layouts.master')

@section('title')
    Create New {{ $pageHeader['title'] }}
@endsection

@push('styles')
    <!-- Add Select2 CSS for better dropdown UI -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
@endpush


@section('admin-content')

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
                                <fieldset>
                                    <div class="row">
                                        <!-- Doctor Name -->
                                        <div class="form-group col-md-6">
                                            <x-default.label required="true" for="reefer_id">Doctor Name</x-default.label>
                                            <select class="form-control" name="reefer_id" id="reefer_id">
                                                <option value="">--Choose--</option>
                                                @foreach($reefers as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                            <x-default.input-error name="reefer_id" />
                                        </div>

                                        <!-- Patient Name -->
                                        <div class="form-group col-md-6">
                                            <x-default.label required="true" for="patient_name">Patient Name</x-default.label>
                                            <x-default.input name="patient_name" class="form-control" id="patient_name" type="text" />
                                            <x-default.input-error name="patient_name" />
                                        </div>

                                        <!-- Patient Phone -->
                                        <div class="form-group col-md-6">
                                            <x-default.label for="patient_phone">Patient Phone Number</x-default.label>
                                            <x-default.input name="patient_phone" class="form-control" id="patient_phone" type="text" placeholder="Enter phone number" />
                                            <x-default.input-error name="patient_phone" />
                                        </div>
                                        <!-- Patient Phone -->
                                        <div class="form-group col-md-6">
                                            <x-default.label for="patient_age_year">Patient Age</x-default.label>
                                            <x-default.input name="patient_age_year" class="form-control"
                                                             id="patient_age_year" type="text"
                                                             placeholder="Enter Age"/>
                                            <x-default.input-error name="patient_age_year"/>
                                        </div>

                                        <!-- Patient Phone -->
                                        <div class="form-group col-md-6">
                                            <x-default.label for="serial_number">Serial No</x-default.label>
                                            <x-default.input name="serial_number" class="form-control" id="serial_number" type="text" placeholder="Enter Serial number" />
                                            <x-default.input-error name="serial_number" />
                                        </div>

                                        <!-- Date -->
                                        <div class="form-group col-md-6">
                                            <x-default.label for="date">Date <strong class="text-danger">*</strong></x-default.label>
                                            <input id="date"
                                                   class="form-control"
                                                   name="date"
                                                   type="date"
                                                   value="{{ old('date', \Carbon\Carbon::now()->format('Y-m-d')) }}"
                                                   min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                            @error('date')
                                            <strong class="text-danger">{{ $message }}</strong>
                                            @enderror
                                        </div>

                                        <!-- Remarks -->
                                        <div class="form-group col-md-6">
                                            <x-default.label for="remarks">Remarks</x-default.label>
                                            <x-default.input name="remarks" class="form-control" id="remarks" type="text" placeholder="Enter Remarks" />
                                            <x-default.input-error name="remarks" />
                                        </div>
                                    </div>

                                    <!-- Patient Phone -->
                                    <div class="form-group col-md-6">
                                        <x-default.label for="send_sms">Send sms</x-default.label>
                                        <input name="send_sms" checked id="send_sms" type="radio" value="yes"/>Yes
                                        <input name="send_sms" id="send_sms" type="radio" value="no"/>No
                                        <x-default.input-error name="send_sms" />
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

            $('#reefer_id').select2({
                placeholder: "Select a Doctor",
                allowClear: true
            });
        });
    </script>
@endpush
