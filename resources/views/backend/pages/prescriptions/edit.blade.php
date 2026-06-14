@extends('backend.layouts.master')

@section('title')
    Edit {{ $pageHeader['title'] }}
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
            'formTitle' => 'Edit Prescription',
            'formSubtitle' => ($prescription->invoice->patient_name ?? 'Patient') . ' · ' . ($prescription->doctor->name ?? ''),
            'formIcon' => 'fa-pen-to-square',
        ])

        <div class="crud-card">
            @include('backend.layouts.partials.message')

            <form action="{{ route($pageHeader['update_route'], $prescription->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="crud-form-section">
                    <div class="crud-form-section-header"><i class="fas fa-user-md"></i> Doctor & Patient</div>
                    <div class="crud-form-section-body">
                        <div class="row crud-form-grid g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="reefer_id">Doctor <span class="text-danger">*</span></label>
                                @if($linkedDoctor)
                                    <input type="hidden" name="reefer_id" value="{{ $linkedDoctor->id }}">
                                    <input type="text" class="form-control" readonly value="{{ $linkedDoctor->name }}">
                                @else
                                    <select name="reefer_id" id="reefer_id" class="form-select cost-category-select" required data-placeholder="Search doctor...">
                                        @foreach($reefers as $reefer)
                                            <option value="{{ $reefer->id }}" @selected(old('reefer_id', $prescription->reefer_id) == $reefer->id)>{{ $reefer->name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                                @error('reefer_id')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Patient</label>
                                <input type="text" class="form-control" readonly
                                       value="{{ $prescription->invoice->patient_name ?? 'N/A' }} · Age {{ $prescription->invoice->patient_age_year ?? '—' }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="crud-form-section">
                    <div class="crud-form-section-header"><i class="fas fa-stethoscope"></i> Clinical Notes</div>
                    <div class="crud-form-section-body">
                        <div class="row crud-form-grid g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="diagnosis">Diagnosis (C/C)</label>
                                <textarea name="diagnosis" id="diagnosis" class="form-control" rows="4">{{ old('diagnosis', $prescription->diagnosis) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="investigation">Investigation (O/E)</label>
                                <textarea name="investigation" id="investigation" class="form-control" rows="4">{{ old('investigation', $prescription->investigation) }}</textarea>
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
                        <div id="drugContainer">
                            @foreach($prescription->drugs as $drug)
                                <div class="rx-drug-item row g-2 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label">Drug Name</label>
                                        <input type="text" name="drug_name[]" class="form-control" value="{{ $drug->name }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Rule</label>
                                        <input type="text" name="drug_rule[]" class="form-control" value="{{ $drug->rule }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Time</label>
                                        <input type="text" name="drug_time[]" class="form-control" value="{{ $drug->time }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Duration</label>
                                        <input type="text" name="drug_duration[]" class="form-control" value="{{ $drug->duration }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Note</label>
                                        <input type="text" name="drug_note[]" class="form-control" value="{{ $drug->note }}">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-outline-danger w-100 removeDrug"><i class="fas fa-times"></i></button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-muted small mb-0 @if($prescription->drugs->count()) d-none @endif" id="drugEmptyHint">No medicines. Click "Add Drug".</p>
                    </div>
                </div>

                <div class="crud-form-actions">
                    <a href="{{ route($pageHeader['index_route']) }}" class="btn-crud-cancel">Cancel</a>
                    <a href="{{ route($pageHeader['show_route'], $prescription->id) }}" class="btn btn-outline-primary">View / Print</a>
                    <button type="submit" class="btn btn-crud-submit">Update Prescription</button>
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
                    <div class="col-md-3"><label class="form-label">Drug Name</label><input type="text" name="drug_name[]" class="form-control"></div>
                    <div class="col-md-2"><label class="form-label">Rule</label><input type="text" name="drug_rule[]" class="form-control"></div>
                    <div class="col-md-2"><label class="form-label">Time</label><input type="text" name="drug_time[]" class="form-control"></div>
                    <div class="col-md-2"><label class="form-label">Duration</label><input type="text" name="drug_duration[]" class="form-control"></div>
                    <div class="col-md-2"><label class="form-label">Note</label><input type="text" name="drug_note[]" class="form-control"></div>
                    <div class="col-md-1"><button type="button" class="btn btn-outline-danger w-100 removeDrug"><i class="fas fa-times"></i></button></div>
                </div>`;
        }

        document.addEventListener('DOMContentLoaded', function () {
            $('#reefer_id').select2({ placeholder: 'Search doctor...', allowClear: true, width: '100%', minimumResultsForSearch: 0 });
            $('#addDrug').on('click', function () {
                $('#drugEmptyHint').addClass('d-none');
                $('#drugContainer').append(drugRowHtml());
            });
            $(document).on('click', '.removeDrug', function () {
                $(this).closest('.rx-drug-item').remove();
                if (!$('#drugContainer .rx-drug-item').length) $('#drugEmptyHint').removeClass('d-none');
            });
        });
    </script>
@endpush
