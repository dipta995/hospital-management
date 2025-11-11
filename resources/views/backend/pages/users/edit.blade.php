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
                            <h4 class="card-title">Modify  <strong>{{ $edited->name }}'s</strong> Information</h4>
                            @include('backend.layouts.partials.message')
                            <form class="cmxform" method="post" action="{{ route($pageHeader['update_route'], $edited->id) }}">
                                @method('PUT')
                                @csrf
                                <fieldset class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name">Name <strong class="text-danger">*</strong></label>
                                            <input id="name" class="form-control @error('name') is-invalid @enderror" name="name" type="text" value="{{ old('name',$edited->name) }}">
                                            @error('name')
                                            <strong class="text-danger">{{ $errors->first('name') }}</strong>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="phone">Phone <strong class="text-danger">*</strong></label>
                                            <input id="phone" class="form-control @error('phone') is-invalid @enderror" name="phone" type="text" value="{{ old('phone',$edited->phone) }}">
                                            @error('phone')
                                            <strong class="text-danger">{{ $errors->first('phone') }}</strong>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="age">Age <strong class="text-danger">*</strong></label>
                                            <input id="age" class="form-control @error('age') is-invalid @enderror" name="age" type="text" value="{{ old('age',$edited->age) }}">
                                            @error('age')
                                            <strong class="text-danger">{{ $errors->first('age') }}</strong>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="address">Address <strong class="text-danger">*</strong></label>
                                            <input id="address" class="form-control @error('address') is-invalid @enderror" name="address" type="text" value="{{ old('address',$edited->address) }}">
                                            @error('address')
                                            <strong class="text-danger">{{ $errors->first('address') }}</strong>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <x-default.label required="true" for="blood_group">Blood Group</x-default.label>
                                            <select class="form-control" name="blood_group" id="blood_group">
                                                <option @selected($edited->blood_group=='A+') value="A+">A+</option>
                                                <option @selected($edited->blood_group=='A-') value="A-">A-</option>
                                                <option @selected($edited->blood_group=='B+') value="B+">B+</option>
                                                <option @selected($edited->blood_group=='B-') value="B-">B-</option>
                                                <option @selected($edited->blood_group=='AB+') value="AB+">AB+</option>
                                                <option @selected($edited->blood_group=='AB-') value="AB-">AB-</option>
                                                <option @selected($edited->blood_group=='O+') value="O+">O+</option>
                                                <option @selected($edited->blood_group=='O-') value="O-">O-</option>
                                            </select>
                                            <x-default.input-error name="blood_group"></x-default.input-error>
                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <x-default.label required="true" for="gender">Gender</x-default.label>
                                            <select class="form-control" name="gender" id="gender">
                                                <option @selected($edited->gender=='Male') value="Male">Male</option>
                                                <option @selected($edited->gender=='Female') value="Female">Female</option>
                                                <option @selected($edited->gender=='Other') value="Other">Other</option>
                                            </select>
                                            <x-default.input-error name="gender"></x-default.input-error>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="marital_status">Marital Status</label>
                                            <select class="form-control" name="marital_status" id="marital_status">
                                                <option value="">-- Select Marital Status --</option>
                                                <option @selected($edited->marital_status=='Single') value="Single">Single</option>
                                                <option @selected($edited->marital_status=='Married') value="Married">Married</option>
                                                <option @selected($edited->marital_status=='Divorced') value="Divorced">Divorced</option>
                                                <option @selected($edited->marital_status=='Widowed') value="Widowed">Widowed</option>
                                            </select>
                                            @error('marital_status')
                                                <strong class="text-danger">{{ $errors->first('marital_status') }}</strong>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="occupation">Occupation</label>
                                            <input id="occupation" class="form-control @error('occupation') is-invalid @enderror" name="occupation" type="text" value="{{ old('occupation', $edited->occupation) }}">
                                            @error('occupation')
                                                <strong class="text-danger">{{ $errors->first('occupation') }}</strong>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="religion">Religion</label>
                                            <input id="religion" class="form-control @error('religion') is-invalid @enderror" name="religion" type="text" value="{{ old('religion', $edited->religion) }}">
                                            @error('religion')
                                                <strong class="text-danger">{{ $errors->first('religion') }}</strong>
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
