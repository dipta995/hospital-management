@extends('backend.layouts.master')
@section('title')
    {{ $pageHeader['title'] }}
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
                            <h4 class="card-title">Create New Patient</h4>
                            @include('backend.layouts.partials.message')
                            <form  method="post" action="{{ route($pageHeader['store_route']) }}">
                                @csrf
                                <fieldset class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name">Name <strong class="text-danger">*</strong></label>
                                            <input id="name" class="form-control @error('name') is-invalid @enderror" name="name" type="text" value="{{ old('name') }}">
                                            @error('name')
                                            <strong class="text-danger">{{ $errors->first('name') }}</strong>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="phone">Phone <strong class="text-danger">*</strong></label>
                                            <input id="phone" class="form-control @error('phone') is-invalid @enderror" name="phone" type="text" value="{{ old('phone') }}">
                                            @error('phone')
                                            <strong class="text-danger">{{ $errors->first('phone') }}</strong>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="age">Age <strong class="text-danger">*</strong></label>
                                            <input id="age" class="form-control @error('age') is-invalid @enderror" name="age" type="text" value="{{ old('age') }}">
                                            @error('age')
                                            <strong class="text-danger">{{ $errors->first('age') }}</strong>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="address">Address <strong class="text-danger">*</strong></label>
                                            <input id="address" class="form-control @error('address') is-invalid @enderror" name="address" type="text" value="{{ old('address') }}">
                                            @error('address')
                                            <strong class="text-danger">{{ $errors->first('address') }}</strong>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                            <div class="form-group">
                                                <x-default.label required="true" for="blood_group">Blood Group</x-default.label>
                                                <select class="form-control" name="blood_group" id="blood_group">
                                                    <option value="">--Select--</option>
                                                    <option value="A+">A+</option>
                                                    <option value="A-">A-</option>
                                                    <option value="B+">B+</option>
                                                    <option value="B-">B-</option>
                                                    <option value="AB+">AB+</option>
                                                    <option value="AB-">AB-</option>
                                                    <option value="O+">O+</option>
                                                    <option value="O-">O-</option>
                                                </select>
                                                <x-default.input-error name="blood_group"></x-default.input-error>
                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <x-default.label required="true" for="gender">Gender</x-default.label>
                                            <select class="form-control" name="gender" id="gender">
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                                <option value="Other">Other</option>
                                            </select>
                                            <x-default.input-error name="gender"></x-default.input-error>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="marital_status">Marital Status</label>
                                            <select class="form-control" name="marital_status" id="marital_status">
                                                <option value="">--Select--</option>
                                                <option value="Single">Single</option>
                                                <option value="Married">Married</option>
                                                <option value="Divorced">Divorced</option>
                                                <option value="Widowed">Widowed</option>
                                            </select>
                                            @error('marital_status')
                                                <strong class="text-danger">{{ $message }}</strong>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="occupation">Occupation</label>
                                            <input id="occupation" class="form-control @error('occupation') is-invalid @enderror" name="occupation" type="text" value="{{ old('occupation') }}">
                                            @error('occupation')
                                                <strong class="text-danger">{{ $message }}</strong>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="religion">Religion</label>
                                            <input id="religion" class="form-control @error('religion') is-invalid @enderror" name="religion" type="text" value="{{ old('religion') }}">
                                            @error('religion')
                                                <strong class="text-danger">{{ $message }}</strong>
                                            @enderror
                                        </div>
                                    </div>
                                    



{{--                                    <div class="col-md-4">--}}
{{--                                        <div class="form-group">--}}
{{--                                            <label for="password">Password <strong class="text-danger">*</strong></label>--}}
{{--                                            <input class="form-control @error('password') is-invalid @enderror" name="password" type="password">--}}
{{--                                            @error('password')--}}
{{--                                            <strong class="text-danger">{{ $errors->first('password') }}</strong>--}}
{{--                                            @enderror--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-md-4">--}}
{{--                                        <div class="form-group">--}}
{{--                                            <label for="password_confirmation">Confirm password <strong class="text-danger">*</strong></label>--}}
{{--                                            <input class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" type="password">--}}
{{--                                            @error('password_confirmation')--}}
{{--                                            <strong class="text-danger">{{ $errors->first('password_confirmation') }}</strong>--}}
{{--                                            @enderror--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
                                    <div class="col-md-12">
                                    <input class="btn btn-primary" type="submit" value="Submit">
                                    </div>
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

@endpush
