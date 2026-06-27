@extends('backend.layouts.master')
@section('title')
    Create New {{ $pageHeader['title'] }}
@endsection

@push('styles')
    @include('backend.layouts.partials.lab-styles')
    <style>
        .tr-create-layout { display: grid; grid-template-columns: 280px 1fr; gap: 20px; }
        @media (max-width: 991px) { .tr-create-layout { grid-template-columns: 1fr; } }
        .tr-test-sidebar {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 14px;
            max-height: 520px;
            overflow-y: auto;
        }
        .tr-category-title {
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            margin: 12px 0 8px;
        }
        .tr-test-link {
            display: block;
            padding: 10px 12px;
            margin-bottom: 6px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            background: #fff;
            text-decoration: none;
            color: #0f172a;
            font-size: 0.88rem;
            font-weight: 600;
            transition: all 0.12s ease;
        }
        .tr-test-link:hover { border-color: #93c5fd; background: #eff6ff; color: #1d4ed8; }
        .tr-test-link.is-added { opacity: 0.55; pointer-events: none; background: #f1f5f9; }
        .tr-invoice-banner {
            background: linear-gradient(135deg, #eff6ff, #f0fdf4);
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 16px;
            font-size: 0.88rem;
        }
    </style>
@endpush

@section('admin-content')
<div class="lab-page crud-page container-fluid py-3">
    @include('backend.layouts.partials.crud-form-hero', [
        'formTitle' => 'Create Test Report',
        'formSubtitle' => $invoice ? 'Invoice #' . $invoice->invoice_number . ' · ' . $invoice->patient_name : 'Select tests from invoice',
        'formIcon' => 'fa-file-medical',
        'formBackUrl' => ($invoiceId && $invoice) ? route('admin.labs.show', $invoice->id) : route('admin.test_reports.index'),
        'formBackLabel' => ($invoiceId && $invoice) ? 'Back to Lab' : 'Back',
    ])

    @if($invoiceId && $invoice)
        <div class="tr-invoice-banner">
            <strong>Invoice #{{ $invoice->invoice_number }}</strong>
            · Patient: {{ $invoice->patient_name }}
            · ID: {{ $invoice->patient_no }}
            <a href="{{ route('admin.labs.show', $invoice->id) }}" class="btn btn-sm btn-outline-primary ms-2">Back to Lab</a>
        </div>
    @elseif($invoiceId && !$invoice)
        <div class="alert alert-warning">Invoice not found or access denied.</div>
    @endif

    @include('backend.layouts.partials.message')

    <div class="crud-card p-3">
        <form method="post" action="{{ route($pageHeader['store_route']) }}">
            @csrf
            <input type="hidden" name="invoiceId" value="{{ $invoiceId }}">
            <input type="hidden" name="testReport" id="testReportInput" value="{{ $testReport }}">

            <div class="tr-create-layout">
                <div class="tr-test-sidebar">
                    <h6 class="fw-bold mb-2"><i class="fas fa-vial me-1"></i> Invoice Tests</h6>
                    <p class="small text-muted mb-2">Click a test to insert parameter table into the editor.</p>

                    @php
                        $groupedTests = $tests->groupBy(function ($test) {
                            return optional(optional($test->product)->category)->name ?? 'Uncategorized';
                        });
                    @endphp

                    @forelse($groupedTests as $categoryName => $categoryTests)
                        <div class="tr-category-title">{{ $categoryName }}</div>
                        @foreach($categoryTests as $item)
                            <a href="#"
                               class="tr-test-link js-test-link"
                               data-product-id="{{ optional($item->product)->id }}"
                               data-id="{{ $item->id }}"
                               data-name="{{ optional($item->product)->name ?? 'N/A' }}">
                                {{ optional($item->product)->name ?? 'N/A' }}
                                <span class="d-block small text-muted fw-normal">{{ $item->status }}</span>
                            </a>
                        @endforeach
                    @empty
                        <div class="text-muted small py-3 text-center">
                            @if($invoiceId)
                                No tests on this invoice.
                            @else
                                Open from Lab Work page with <code>?invoiceId=</code>
                            @endif
                        </div>
                    @endforelse
                </div>

                <div>
                    <label class="form-label fw-semibold" for="report">Test Report Editor <span class="text-danger">*</span></label>
                    <textarea name="report" id="report" class="form-control" rows="18">{{ old('report') }}</textarea>
                    @error('report')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        @if($invoiceId)
                            <a href="{{ route('admin.labs.show', $invoiceId) }}" class="btn btn-outline-secondary">Cancel</a>
                        @endif
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Save Report
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
    <script>
        let reportEditor = null;
        const addedTests = new Set();
        const parametersUrl = @json(url('/admin/get-product-parameters'));

        function buildParameterTable(testName, parameters) {
            let rows = '';
            if (parameters && parameters.length > 0) {
                parameters.forEach(function (p) {
                    rows += '<tr>'
                        + '<td>' + (p.parameter || '') + '</td>'
                        + '<td>&nbsp;</td>'
                        + '<td>' + (p.unit || '') + '</td>'
                        + '<td>' + (p.reference_range || '') + '</td>'
                        + '</tr>';
                });
            } else {
                rows = '<tr><td colspan="4"><em>No parameters defined for this test</em></td></tr>';
            }

            return '<h3>' + testName + '</h3>'
                + '<figure class="table"><table>'
                + '<thead><tr><th>Parameter</th><th>Result</th><th>Unit</th><th>Reference Range</th></tr></thead>'
                + '<tbody>' + rows + '</tbody></table></figure>';
        }

        function attachTestClickHandlers() {
            document.querySelectorAll('.js-test-link').forEach(function (link) {
                link.addEventListener('click', function (e) {
                    e.preventDefault();

                    const testId = this.dataset.id;
                    const productId = this.dataset.productId;
                    const testName = this.dataset.name || '';

                    if (!testId || !productId) return;
                    if (addedTests.has(testId)) return;
                    addedTests.add(testId);

                    const hiddenInput = document.getElementById('testReportInput');
                    if (hiddenInput) hiddenInput.value = testId;

                    this.classList.add('is-added');

                    fetch(parametersUrl + '/' + productId, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin',
                    })
                        .then(function (res) { return res.ok ? res.json() : []; })
                        .then(function (parameters) {
                            const snippet = buildParameterTable(testName, parameters);
                            const currentData = reportEditor ? reportEditor.getData() : '';
                            const separator = currentData.trim() ? '<hr />' : '';
                            if (reportEditor) {
                                reportEditor.setData(currentData + separator + snippet);
                            }
                        })
                        .catch(function () {
                            const snippet = '<h3>' + testName + '</h3>';
                            const currentData = reportEditor ? reportEditor.getData() : '';
                            if (reportEditor) reportEditor.setData(currentData + snippet);
                        });
                });
            });
        }

        (function () {
            const reportField = document.querySelector('#report');
            const form = reportField ? reportField.closest('form') : null;
            if (!reportField) return;

            ClassicEditor.create(reportField, {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'undo', 'redo']
            })
                .then(function (editor) {
                    reportEditor = editor;
                    attachTestClickHandlers();

                    const preselected = document.getElementById('testReportInput')?.value;
                    if (preselected) {
                        const link = document.querySelector('.js-test-link[data-id="' + preselected + '"]');
                        if (link) link.click();
                    }

                    if (form) {
                        form.addEventListener('submit', function () {
                            editor.updateSourceElement();
                        });
                    }
                })
                .catch(function (error) {
                    console.error(error);
                    attachTestClickHandlers();
                });
        })();
    </script>
@endpush
