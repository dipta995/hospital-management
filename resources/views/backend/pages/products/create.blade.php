@extends('backend.layouts.master')
@section('title')
    Create {{ $pageHeader['title'] }}
@endsection

@push('styles')
    @include('backend.layouts.partials.crud-styles')
@endpush

@section('admin-content')
    <div class="crud-page container-fluid py-3">
        @include('backend.layouts.partials.crud-form-hero', [
            'formTitle' => 'Create Lab Test',
            'formSubtitle' => 'Add test details, pricing and parameters',
            'formIcon' => 'fa-flask',
        ])

        <div class="crud-card">
            @include('backend.layouts.partials.message')

            <form method="post" action="{{ route($pageHeader['store_route']) }}">
                @csrf

                <div class="crud-form-section">
                    <div class="crud-form-section-header">
                        <i class="fas fa-info-circle"></i> Basic Information
                    </div>
                    <div class="crud-form-section-body">
                        <div class="row crud-form-grid g-3">
                            <div class="col-md-4">
                                <x-default.label required="true" for="category_id">Category</x-default.label>
                                <select class="form-select" name="category_id" id="category_id">
                                    <option value="">-- Choose Category --</option>
                                    @foreach($categories as $item)
                                        <option value="{{ $item->id }}" @selected(old('category_id') == $item->id)>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                <x-default.input-error name="category_id"></x-default.input-error>
                            </div>
                            <div class="col-md-4">
                                <x-default.label required="true" for="name">Test Name</x-default.label>
                                <x-default.input name="name" class="form-control" id="name" type="text" value="{{ old('name') }}"></x-default.input>
                                <x-default.input-error name="name"></x-default.input-error>
                            </div>
                            <div class="col-md-4">
                                <label for="code">Code</label>
                                <input id="code" class="form-control" name="code" type="number" value="{{ old('code', $nextCode) }}">
                                @error('code')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="price">Price <span class="text-danger">*</span></label>
                                <input id="price" class="form-control" name="price" type="number" step="0.01" value="{{ old('price') }}">
                                @error('price')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="reefer_fee">Refer Fee <span class="text-danger">*</span></label>
                                <input id="reefer_fee" class="form-control" name="reefer_fee" type="number" step="0.01" value="{{ old('reefer_fee') }}">
                                @error('reefer_fee')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="crud-form-section">
                    <div class="crud-form-section-header">
                        <i class="fas fa-list-check"></i> Test Parameters
                    </div>
                    <div class="crud-form-section-body">
                        <div class="crud-table-wrap">
                            <div class="table-responsive">
                                <table class="table crud-table table-bordered" id="parameterTable">
                                    <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Unit</th>
                                        <th>Reference Range</th>
                                        <th style="width: 80px;" class="text-center">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $parameterRows = old('parameters', [['parameter' => '', 'unit' => '', 'reference_range' => '']]);
                                    @endphp
                                    @foreach($parameterRows as $index => $param)
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control" name="parameters[{{ $index }}][parameter]"
                                                       value="{{ $param['parameter'] ?? '' }}" placeholder="e.g. Hemoglobin">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="parameters[{{ $index }}][unit]"
                                                       value="{{ $param['unit'] ?? '' }}" placeholder="e.g. g/dL">
                                            </td>
                                            <td>
                                                <textarea class="form-control" rows="2" name="parameters[{{ $index }}][reference_range]"
                                                          placeholder="e.g. 12 - 16 (Male), 11 - 15 (Female)">{{ $param['reference_range'] ?? '' }}</textarea>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm remove-row">X</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addParameterRow">
                            <i class="fas fa-plus"></i> Add Parameter Row
                        </button>

                        @if ($errors->has('parameters') || $errors->has('parameters.*.parameter') || $errors->has('parameters.*.unit') || $errors->has('parameters.*.reference_range'))
                            <div class="mt-2 text-danger small">
                                {{ $errors->first('parameters') }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="crud-form-actions">
                    <a href="{{ route($pageHeader['index_route']) }}" class="btn-crud-cancel">Cancel</a>
                    <button type="submit" class="btn btn-crud-submit">Create Test</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const tableBody = document.querySelector('#parameterTable tbody');
            const addButton = document.getElementById('addParameterRow');

            function getNextIndex() {
                return tableBody.querySelectorAll('tr').length;
            }

            function newRow(index) {
                return `
                    <tr>
                        <td><input type="text" class="form-control" name="parameters[${index}][parameter]" placeholder="e.g. Hemoglobin"></td>
                        <td><input type="text" class="form-control" name="parameters[${index}][unit]" placeholder="e.g. g/dL"></td>
                        <td><textarea class="form-control" rows="2" name="parameters[${index}][reference_range]" placeholder="e.g. 12 - 16 (Male), 11 - 15 (Female)"></textarea></td>
                        <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row">X</button></td>
                    </tr>
                `;
            }

            addButton.addEventListener('click', function () {
                tableBody.insertAdjacentHTML('beforeend', newRow(getNextIndex()));
            });

            tableBody.addEventListener('click', function (event) {
                if (!event.target.classList.contains('remove-row')) {
                    return;
                }
                const rows = tableBody.querySelectorAll('tr');
                if (rows.length === 1) {
                    rows[0].querySelectorAll('input, textarea').forEach(input => input.value = '');
                    return;
                }
                event.target.closest('tr').remove();
            });
        })();
    </script>
@endpush
