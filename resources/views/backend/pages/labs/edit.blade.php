@extends('backend.layouts.master')
@section('title')
    Edit Lab Report
@endsection
@push('styles')
    @include('backend.layouts.partials.lab-styles')
@endpush
@section('admin-content')
    <div class="lab-page crud-page container-fluid py-3">
        @include('backend.layouts.partials.crud-form-hero', [
            'formTitle' => 'Edit Test Report',
            'formSubtitle' => ($edited->product->name ?? 'Test') . ' · ' . ($edited->invoice->patient_name ?? 'Patient'),
            'formIcon' => 'fa-file-medical',
            'formBackRoute' => 'admin.labs.index',
            'formBackLabel' => 'Back to Lab Queue',
        ])

        @include('backend.layouts.partials.message')

        <div class="crud-card">
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="lab-kpi">
                        <div class="lab-kpi-label">Invoice</div>
                        <div class="lab-kpi-value" style="font-size:1rem;">{{ $edited->invoice->invoice_number ?? '—' }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="lab-kpi">
                        <div class="lab-kpi-label">Test</div>
                        <div class="lab-kpi-value" style="font-size:1rem;">{{ $edited->product->name ?? '—' }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="lab-kpi">
                        <div class="lab-kpi-label">Patient</div>
                        <div class="lab-kpi-value" style="font-size:1rem;">{{ $edited->invoice->patient_name ?? '—' }}</div>
                    </div>
                </div>
            </div>

            <form method="post" action="{{ route($pageHeader['update_route'], [$edited->id]) . '?status=' . request('status') }}"
                  enctype="multipart/form-data">
                @method('PUT')
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="file">Document File</label>
                        <input type="file" name="file" id="file" class="form-control">
                        @if(!empty($edited->document))
                            <div class="mt-2">
                                <a class="btn btn-sm btn-outline-success" target="_blank"
                                   href="{{ route('admin.lab.report.file-download', $edited->id) }}">
                                    <i class="fas fa-download"></i> Current File
                                </a>
                            </div>
                        @else
                            <div class="small text-muted mt-2">No file uploaded yet.</div>
                        @endif
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        @if($edited->invoice_id)
                            <a href="{{ route('admin.labs.show', $edited->invoice_id) }}" class="btn btn-outline-primary">
                                <i class="fas fa-flask"></i> Open Lab Work
                            </a>
                        @endif
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="description">Report Content <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" rows="12" class="form-control">{!! $edited->test_report ?? $edited->product->description !!}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('admin.labs.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-crud-submit">
                        <i class="fas fa-save me-1"></i> Update Report
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
    <script>
        ClassicEditor.create(document.querySelector('#description'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'undo', 'redo']
        }).catch(console.error);
    </script>
@endpush
