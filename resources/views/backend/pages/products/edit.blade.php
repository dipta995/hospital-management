@extends('backend.layouts.master')
@section('title')
    Edit {{ $pageHeader['title'] }}
@endsection

@push('styles')
    @include('backend.layouts.partials.crud-styles')
@endpush

@section('admin-content')
    <div class="crud-page container-fluid py-3">
        @include('backend.layouts.partials.crud-form-hero', [
            'formTitle' => 'Edit Lab Test',
            'formSubtitle' => 'Update ' . $edited->name,
            'formIcon' => 'fa-flask',
        ])

        <div class="crud-card">
            @include('backend.layouts.partials.message')

            <form method="post" action="{{ route($pageHeader['update_route'], $edited->id) }}">
                @method('PUT')
                @csrf

                <div class="crud-form-section">
                    <div class="crud-form-section-header">
                        <i class="fas fa-info-circle"></i> Basic Information
                    </div>
                    <div class="crud-form-section-body">
                        <div class="row crud-form-grid g-3">
                            <div class="col-md-4">
                                <label for="name">Test Name <span class="text-danger">*</span></label>
                                <input id="name" class="form-control" name="name" type="text" value="{{ old('name', $edited->name) }}">
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="code">Code <span class="text-danger">*</span></label>
                                <input id="code" class="form-control" name="code" type="number" value="{{ old('code', $edited->code) }}">
                                @error('code')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="price">Price <span class="text-danger">*</span></label>
                                <input id="price" class="form-control" name="price" type="number" step="0.01" value="{{ old('price', $edited->price) }}">
                                @error('price')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="reefer_fee">Refer Fee <span class="text-danger">*</span></label>
                                <input id="reefer_fee" class="form-control" name="reefer_fee" type="number" step="0.01" value="{{ old('reefer_fee', $edited->reefer_fee) }}">
                                @error('reefer_fee')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Category</label>
                                <input class="form-control" type="text" value="{{ $edited->category->name ?? '—' }}" readonly>
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
                                        $parameterRows = old('parameters');
                                        if (is_null($parameterRows)) {
                                            $parameterRows = $edited->parameters->map(function ($item) {
                                                return [
                                                    'parameter' => $item->parameter,
                                                    'unit' => $item->unit,
                                                    'reference_range' => $item->reference_range,
                                                ];
                                            })->toArray();
                                        }
                                        if (empty($parameterRows)) {
                                            $parameterRows = [['parameter' => '', 'unit' => '', 'reference_range' => '']];
                                        }
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
                    <button type="submit" class="btn btn-crud-submit">Update Test</button>
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
