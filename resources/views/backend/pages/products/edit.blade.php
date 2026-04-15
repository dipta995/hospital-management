@extends('backend.layouts.master')
@section('title')
    Edit {{ $pageHeader['title'] }}
@endsection
@push('styles')

@endpush
@section('admin-content')
    <!-- partial -->
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Modify  <strong>{{ $edited->name }}'s</strong> Information</h4>
                            @include('backend.layouts.partials.message')

                            <form class="cmxform" method="post" action="{{ route($pageHeader['update_route'], $edited->id) }}">
                                @method('PUT')
                                @csrf
                                <fieldset>
                                    <div class="form-group">
                                        <label for="name">Name <strong class="text-danger">*</strong></label>
                                        <input id="name" class="form-control "
                                               name="name" type="text" value="{{ old('name',$edited->name) }}">
                                        @error('name')
                                        <strong class="text-danger">{{ $errors->first('name') }}</strong>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="code">Code<strong
                                                class="text-danger">*</strong></label>
                                        <input id="code"
                                               class="form-control @error('code') is-invalid @enderror"
                                               name="code" type="number"
                                               value="{{ old('rice',$edited->code) }}">
                                        @error('code')
                                        <strong class="text-danger">{{ $errors->first('code') }}</strong>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="price">Price<strong
                                                class="text-danger">*</strong></label>
                                        <input id="price"
                                               class="form-control @error('price') is-invalid @enderror"
                                               name="price" type="number"
                                               value="{{ old('price',$edited->price) }}">
                                        @error('price')
                                        <strong class="text-danger">{{ $errors->first('price') }}</strong>
                                        @enderror
                                    </div>


                                    <div class="form-group">
                                        <label for="reefer_fee">Reefer Fee<strong
                                                class="text-danger">*</strong></label>
                                        <input id="reefer_fee"
                                               class="form-control @error('reefer_fee') is-invalid @enderror"
                                               name="reefer_fee" type="number"
                                               value="{{ old('reefer_fee',$edited->reefer_fee) }}">
                                        @error('reefer_fee')
                                        <strong class="text-danger">{{ $errors->first('reefer_fee') }}</strong>
                                        @enderror
                                    </div>
                                    <hr>
                                    <h5 class="mb-3">Product Parameters</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="parameterTable">
                                            <thead>
                                            <tr>
                                                <th>Parameter</th>
                                                <th>Unit</th>
                                                <th>Reference Range</th>
                                                <th style="width: 80px;">Action</th>
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
                                                        <input type="text" class="form-control"
                                                               name="parameters[{{ $index }}][parameter]"
                                                               value="{{ $param['parameter'] ?? '' }}"
                                                               placeholder="e.g. Hemoglobin">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control"
                                                               name="parameters[{{ $index }}][unit]"
                                                               value="{{ $param['unit'] ?? '' }}"
                                                               placeholder="e.g. g/dL">
                                                    </td>
                                                    <td>
                                                        <textarea class="form-control" rows="2"
                                                                  name="parameters[{{ $index }}][reference_range]"
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
                                    <button type="button" class="btn btn-primary btn-sm" id="addParameterRow">Add Row</button>

                                    @if ($errors->has('parameters') || $errors->has('parameters.*.parameter') || $errors->has('parameters.*.unit') || $errors->has('parameters.*.reference_range'))
                                        <div class="mt-2">
                                            <strong class="text-danger">{{ $errors->first('parameters') }}</strong>
                                            @foreach($errors->get('parameters.*.parameter') as $messages)
                                                @foreach($messages as $msg)
                                                    <div><strong class="text-danger">{{ $msg }}</strong></div>
                                                @endforeach
                                            @endforeach
                                            @foreach($errors->get('parameters.*.unit') as $messages)
                                                @foreach($messages as $msg)
                                                    <div><strong class="text-danger">{{ $msg }}</strong></div>
                                                @endforeach
                                            @endforeach
                                            @foreach($errors->get('parameters.*.reference_range') as $messages)
                                                @foreach($messages as $msg)
                                                    <div><strong class="text-danger">{{ $msg }}</strong></div>
                                                @endforeach
                                            @endforeach
                                        </div>
                                    @endif

                                    <x-default.button class="float-end mt-2 btn-success">Update</x-default.button>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->
        <!-- partial -->
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
