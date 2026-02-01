@extends('backend.layouts.master')
@section('title')
    Create New {{ $pageHeader['title'] }}
@endsection

@push('styles')
    <style>
        .test-list-wrapper {
            max-height: 450px;
            overflow-y: auto;
        }

        .test-category-box {
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 8px 10px;
            margin-bottom: 8px;
            background-color: #f9fafb;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            color: #6b7280;
        }

        .test-item-card {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 8px 10px;
            margin-bottom: 10px;
            background-color: #ffffff;
            cursor: pointer;
            transition: background-color 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
        }

        .test-item-card:hover {
            background-color: #ecfdf5;
            border-color: #22c55e;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        }

        .test-item-card.selected {
            background-color: #dcfce7;
            border-color: #16a34a;
            box-shadow: 0 0 0 1px rgba(22, 163, 74, 0.6);
        }

        .test-item-title {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
        }

        .test-item-subtitle {
            font-size: 12px;
            color: #6b7280;
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

                            <form class="cmxform" method="post" action="{{ route($pageHeader['store_route']) }}">
                                @csrf
                                <fieldset>
                                    <input type="hidden" name="invoiceId" value="{{ $invoiceId }}">
                                    <input type="hidden" name="testReport" id="testReport" value="{{ $testReport }}">

                                    <div class="row">
                                        <div class="col-md-4 border-end">
                                            <h5 class="mb-3">Available Tests</h5>
                                            @php
                                                $grouped = $reportDemo->groupBy('type');
                                            @endphp
                                            <div class="test-list-wrapper">
                                                @foreach($grouped as $type => $tests)
                                                    <div class="mb-3">
                                                        <div class="test-category-box">
                                                            {{ $type }}
                                                        </div>
                                                        @foreach($tests as $item)
                                                            <div class="test-item-card test-template-item" data-demo-id="{{ $item->id }}">
                                                                <div class="test-item-title">{{ $item->name }}</div>
                                                                <div class="test-item-subtitle">{{ $type }}</div>
                                                            </div>
                                                            <textarea id="template_{{ $item->id }}" class="d-none">{!! $item->test_report !!}</textarea>
                                                        @endforeach
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <x-default.label required="true" for="report">Test Report Editor</x-default.label>
                                                <textarea name="report" id="report" class="form-control" cols="30" rows="18">{{ old('report') }}</textarea>
                                                <x-default.input-error name="report"></x-default.input-error>
                                                <small class="text-muted d-block mt-1">Click tests on the left to insert their templates into this editor. You can select multiple tests; their templates will be appended one after another.</small>
                                            </div>
                                        </div>
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
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
    <script>
        let reportEditor;

        ClassicEditor
            .create(document.querySelector('#report'), {
                toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'undo', 'redo' ]
            })
            .then(editor => {
                reportEditor = editor;
            })
            .catch(error => {
                console.error(error);
            });

        document.addEventListener('DOMContentLoaded', function () {
            const selectedTests = new Set();

            document.querySelectorAll('.test-template-item').forEach(function (item) {
                item.addEventListener('click', function () {
                    const demoId = this.getAttribute('data-demo-id');

                    // Prevent adding the same test multiple times (e.g., on double-click)
                    if (selectedTests.has(demoId)) {
                        return;
                    }

                    const templateEl = document.getElementById('template_' + demoId);
                    if (!templateEl || !reportEditor) return;

                    const templateHtml = templateEl.value || templateEl.innerHTML;

                    // Mark as selected for visual feedback and duplicate prevention
                    selectedTests.add(demoId);
                    this.classList.add('selected');

                    // Set first selected template id into hidden field if empty
                    const testReportInput = document.getElementById('testReport');
                    if (testReportInput && !testReportInput.value) {
                        testReportInput.value = demoId;
                    }

                    const currentData = reportEditor.getData();
                    const separator = currentData.trim() ? '<hr />' : '';
                    reportEditor.setData(currentData + separator + templateHtml);
                });
            });
        });
    </script>
@endpush
