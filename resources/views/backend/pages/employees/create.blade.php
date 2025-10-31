@extends('backend.layouts.master')
@section('title')
    Create New {{ $pageHeader['title'] }}
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
                            <h4 class="card-title">Create New {{ $pageHeader['title'] }}</h4>
                            @include('backend.layouts.partials.message')
                            <form class="cmxform" method="post" action="{{ route($pageHeader['store_route']) }}">
                                @csrf
                                <fieldset>
                                    <div class="form-group">
                                        <x-default.label required="true" for="name">Name</x-default.label>
                                        <x-default.input name="name" class="form-control" id="name" type="text"></x-default.input>
                                        <x-default.input-error name="name"></x-default.input-error>
                                    </div>

                                    <div class="form-group">
                                        <label for="designation">Designation</label>
                                        <input id="designation"
                                               class="form-control"
                                               name="designation" type="text" value="{{ old('designation') }}">
                                        @error('designation')
                                        <strong class="text-danger">{{ $errors->first('designation') }}</strong>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="phone">Phone</label>
                                        <input id="phone"
                                               class="form-control"
                                               name="phone" type="text" value="{{ old('phone') }}">
                                        @error('phone')
                                        <strong class="text-danger">{{ $errors->first('phone') }}</strong>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="salary">Salary</label>
                                        <input id="salary"
                                               class="form-control"
                                               name="salary" type="number" value="{{ old('salary') }}">
                                        @error('salary')
                                        <strong class="text-danger">{{ $errors->first('salary') }}</strong>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="rfid">RFID</label>
                                        <input id="rfid"
                                               class="form-control"
                                               name="rfid" type="number" value="{{ old('rfid') }}">
                                        @error('rfid')
                                        <strong class="text-danger">{{ $errors->first('rfid') }}</strong>
                                        @enderror
                                    </div>

                                    <x-default.button class="float-end mt-2 btn-success">Create</x-default.button>

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

@endpush
