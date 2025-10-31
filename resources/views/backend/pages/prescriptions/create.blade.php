@extends('backend.layouts.master')

@section('title')
    Create New {{ $pageHeader['title'] }}
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>

    <style>
        .drug-item {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
    </style>
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

                            <form method="post" action="{{ route($pageHeader['store_route'],['doctor'=>$doctorId]) }}">
                                @csrf
                                <fieldset>
                                    {{--                            Invoice --}}
                                    <h4 class="card-title bg-info p-1 mt-3 mb-3">Patient Details</h4>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-default.label required="true" for="patient_name">Patient Name <span
                                                        class="text-danger">*</span></x-default.label>
                                                <x-default.input name="patient_name" class="form-control"
                                                                 id="patient_name" type="text"></x-default.input>
                                                <x-default.input-error name="patient_name"></x-default.input-error>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-default.label required="true" for="patient_age_year">Age <span
                                                        class="text-danger">*</span></x-default.label>
                                                <x-default.input name="patient_age_year" class="form-control"
                                                                 id="patient_age_year" type="text"></x-default.input>
                                                <x-default.input-error name="patient_age_year"></x-default.input-error>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-default.label required="true" for="patient_blood_group">Blood Group
                                                </x-default.label>
                                                <x-default.input name="patient_blood_group" class="form-control"
                                                                 id="patient_blood_group" type="text"></x-default.input>
                                                <x-default.input-error
                                                    name="patient_blood_group"></x-default.input-error>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-default.label required="true" for="patient_gender">Gender
                                                </x-default.label>
                                                <select class="form-control" name="patient_gender" id="patient_gender">
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                                <x-default.input-error name="patient_gender"></x-default.input-error>
                                            </div>
                                        </div>
                                    </div>
                                    <h4 class="card-title bg-info p-1 mt-3 mb-3">Advise</h4>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <x-default.label required="true" for="products">Select Test(s) <span
                                                        class="text-danger">*</span></x-default.label>
                                                <select class="form-control select2" name="product_ids[]" id="products"
                                                        multiple="multiple" required>
                                                    @foreach($products as $item)
                                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                                <x-default.input-error name="product_ids"></x-default.input-error>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-default.label required="true" for="discount">Discount (%)<span
                                                        class="text-danger">*</span></x-default.label>
                                                <x-default.input name="discount" class="form-control"
                                                                 id="discount" max="30" min="0" value="0" type="number"></x-default.input>
                                                <x-default.input-error name="discount"></x-default.input-error>
                                            </div>
                                        </div>


                                    </div>
                                    {{--                            Invoice --}}
                                    <h4 class="card-title bg-info p-1 mt-3 mb-3">More</h4>
                                    <x-default.input-error name="doctor_id"></x-default.input-error>

                                    <div class="form-group">
                                        <x-default.label for="investigation">Investigation</x-default.label>
                                        <textarea name="investigation" class="form-control" id="investigation"
                                                  rows="2"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <x-default.label for="diagnosis">Diagnosis</x-default.label>
                                        <textarea name="diagnosis" class="form-control" id="diagnosis"
                                                  rows="2"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <x-default.label required="true" for="note">Note</x-default.label>
                                        <x-default.input name="note" class="form-control"
                                                         id="note" type="text"></x-default.input>
                                        <x-default.input-error name="note"></x-default.input-error>
                                    </div>

                                    <div class="form-group">
                                        <label>Drugs</label>
                                        <div id="drugContainer"></div>
                                        <button type="button" class="btn btn-primary mt-2" id="addDrug">+ Add Drug
                                        </button>
                                    </div>


                                    <x-default.button class="float-end mt-4 btn-success">Create Prescription
                                    </x-default.button>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $('#addDrug').click(function () {
        $('#drugContainer').append(`
            <div class="drug-item row align-items-end">
                <div class="col-md-3">
                    <label>Drug Name</label>
                    <input type="text" name="drug_name[]" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label>Rule</label>
                    <input type="text" name="drug_rule[]" class="form-control">
                </div>
                <div class="col-md-2">
                    <label>Time</label>
                    <input type="text" name="drug_time[]" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>Note</label>
                    <input type="text" name="drug_note[]" class="form-control">
                </div>
                 <div class="col-md-3">
                    <label>Duration</label>
                    <input type="text" name="drug_duration[]" class="form-control">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger removeDrug mt-4">Remove</button>
                </div>
            </div>
        `);
    });

    $(document).on('click', '.removeDrug', function () {
        $(this).closest('.drug-item').remove();
    });
    $(document).ready(function () {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        // Invoice
        $('.select2').select2({
            placeholder: "Select one or more tests",
            allowClear: true,
            width: '100%'
        });

        // Invoice
        });
</script>
@endpush
