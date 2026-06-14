@extends('backend.layouts.master')

@section('title')
    Create {{ $pageHeader['title'] }}
@endsection

@push('styles')
    @include('backend.layouts.partials.crud-styles')
    @include('backend.layouts.partials.cost-category-select2-assets')
    <style>
        .rx-drug-item {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px;
            margin-bottom: 12px;
            background: #f8fafc;
        }
    </style>
@endpush

@section('admin-content')
    <div class="crud-page container-fluid py-3">
        @include('backend.layouts.partials.crud-form-hero', [
            'formTitle' => 'Create Prescription',
            'formSubtitle' => 'Patient details, advised tests, diagnosis & medicines',
            'formIcon' => 'fa-prescription',
        ])

        <div class="crud-card">
            @include('backend.layouts.partials.message')

            <form method="post" action="{{ route($pageHeader['store_route']) }}">
                @csrf

                @if($doctorId)
                    <input type="hidden" name="doctor" value="{{ $doctorId }}">
                @endif

                <div class="crud-form-section">
                    <div class="crud-form-section-header"><i class="fas fa-user"></i> Patient Details</div>
                    <div class="crud-form-section-body">
                        <div class="row crud-form-grid g-3">
                            @if(empty($doctorId))
                                <div class="col-md-12">
                                    <label class="form-label" for="doctor">Doctor <span class="text-danger">*</span></label>
                                    <select name="doctor" id="doctor" class="form-select cost-category-select" required data-placeholder="Search doctor...">
                                        <option value=""></option>
                                        @foreach($doctors as $doc)
                                            <option value="{{ $doc->id }}" @selected(old('doctor') == $doc->id)>{{ $doc->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('doctor')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>
                            @endif
                            <div class="col-md-4">
                                <x-default.label required="true" for="patient_name">Patient Name</x-default.label>
                                <input name="patient_name" class="form-control" id="patient_name" type="text" value="{{ old('patient_name') }}" required>
                                <x-default.input-error name="patient_name"></x-default.input-error>
                            </div>
                            <div class="col-md-2">
                                <x-default.label required="true" for="patient_age_year">Age</x-default.label>
                                <input name="patient_age_year" class="form-control" id="patient_age_year" type="text" value="{{ old('patient_age_year') }}" required>
                                <x-default.input-error name="patient_age_year"></x-default.input-error>
                            </div>
                            <div class="col-md-3">
                                <x-default.label required="true" for="patient_gender">Gender</x-default.label>
                                <select class="form-select" name="patient_gender" id="patient_gender" required>
                                    @foreach(['Male', 'Female', 'Other'] as $g)
                                        <option value="{{ $g }}" @selected(old('patient_gender', 'Male') === $g)>{{ $g }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <x-default.label for="patient_blood_group">Blood Group</x-default.label>
                                <input name="patient_blood_group" class="form-control" id="patient_blood_group" type="text" value="{{ old('patient_blood_group') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="crud-form-section">
                    <div class="crud-form-section-header"><i class="fas fa-flask"></i> Advised Tests & Billing</div>
                    <div class="crud-form-section-body">
                        <div class="row crud-form-grid g-3">
                            <div class="col-md-8">
                                <x-default.label required="true" for="products">Select Test(s)</x-default.label>
                                <select class="form-select select2-tests" name="product_ids[]" id="products" multiple required>
                                    @foreach($products as $item)
                                        <option value="{{ $item->id }}" @selected(collect(old('product_ids', []))->contains($item->id))>
                                            {{ $item->name }} — ৳{{ number_format($item->price, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-default.input-error name="product_ids"></x-default.input-error>
                            </div>
                            <div class="col-md-4">
                                <x-default.label for="discount">Discount (%)</x-default.label>
                                <input name="discount" class="form-control" id="discount" max="30" min="0" value="{{ old('discount', 0) }}" type="number">
                                <x-default.input-error name="discount"></x-default.input-error>
                            </div>
                            <div class="col-12">
                                <x-default.label for="note">Invoice Note</x-default.label>
                                <input name="note" class="form-control" id="note" type="text" value="{{ old('note') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="crud-form-section">
                    <div class="crud-form-section-header"><i class="fas fa-stethoscope"></i> Clinical Notes</div>
                    <div class="crud-form-section-body">
                        <div class="row crud-form-grid g-3">
                            <div class="col-md-6">
                                <x-default.label for="diagnosis">Diagnosis (C/C)</x-default.label>
                                <textarea name="diagnosis" class="form-control" id="diagnosis" rows="4" placeholder="Chief complaints...">{{ old('diagnosis') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <x-default.label for="investigation">Investigation (O/E)</x-default.label>
                                <textarea name="investigation" class="form-control" id="investigation" rows="4" placeholder="On examination...">{{ old('investigation') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="crud-form-section">
                    <div class="crud-form-section-header d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-pills"></i> Medicines (℞)</span>
                        <button type="button" class="btn btn-sm btn-primary" id="addDrug"><i class="fas fa-plus"></i> Add Drug</button>
                    </div>
                    <div class="crud-form-section-body">
                        <div id="drugContainer"></div>
                        <p class="text-muted small mb-0" id="drugEmptyHint">No medicines added yet. Click "Add Drug" to prescribe.</p>
                    </div>
                </div>

                <div class="crud-form-actions">
                    <a href="{{ route($pageHeader['index_route']) }}" class="btn-crud-cancel">Cancel</a>
                    <button type="submit" class="btn btn-crud-submit">Create Prescription</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function drugRowHtml() {
            return `
                <div class="rx-drug-item row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Drug Name</label>
                        <input type="text" name="drug_name[]" class="form-control" placeholder="Medicine name">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Rule</label>
                        <input type="text" name="drug_rule[]" class="form-control" placeholder="1+0+1">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Time</label>
                        <input type="text" name="drug_time[]" class="form-control" placeholder="After meal">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Duration</label>
                        <input type="text" name="drug_duration[]" class="form-control" placeholder="7 days">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Note</label>
                        <input type="text" name="drug_note[]" class="form-control">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger w-100 removeDrug"><i class="fas fa-times"></i></button>
                    </div>
                </div>`;
        }

        document.addEventListener('DOMContentLoaded', function () {
            $('.select2-tests').select2({ placeholder: 'Search tests...', allowClear: true, width: '100%', minimumResultsForSearch: 0 });
            $('#doctor').select2({ placeholder: 'Search doctor...', allowClear: true, width: '100%', minimumResultsForSearch: 0 });

            $('#addDrug').on('click', function () {
                $('#drugEmptyHint').hide();
                $('#drugContainer').append(drugRowHtml());
            });

            $(document).on('click', '.removeDrug', function () {
                $(this).closest('.rx-drug-item').remove();
                if (!$('#drugContainer .rx-drug-item').length) $('#drugEmptyHint').show();
            });
        });
    </script>
@endpush
