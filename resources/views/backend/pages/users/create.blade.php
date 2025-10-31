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
                            <h4 class="card-title">Create New {{ $pageHeader['title'] }}</h4>
                            @include('backend.layouts.partials.message')
                            <form class="cmxform" method="post" action="{{ route($pageHeader['store_route']) }}">
                                @csrf
                                <fieldset>
                                    <div class="form-group">
                                        <label for="name">Name <strong class="text-danger">*</strong></label>
                                        <input id="name" class="form-control @error('name') is-invalid @enderror" name="name" type="text" value="{{ old('name') }}">
                                        @error('name')
                                        <strong class="text-danger">{{ $errors->first('name') }}</strong>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="username">Username <strong class="text-danger">*</strong></label>
                                        <input id="username" class="form-control @error('username') is-invalid @enderror" name="username" type="text" value="{{ old('username') }}">
                                        @error('username')
                                        <strong class="text-danger">{{ $errors->first('username') }}</strong>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email <strong class="text-danger">*</strong></label>
                                        <input id="email" class="form-control @error('email') is-invalid @enderror" name="email" type="email" value="{{ old('email') }}">
                                        @error('email')
                                        <strong class="text-danger">{{ $errors->first('email') }}</strong>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="password">Password <strong class="text-danger">*</strong></label>
                                        <input class="form-control @error('password') is-invalid @enderror" name="password" type="password">
                                        @error('password')
                                        <strong class="text-danger">{{ $errors->first('password') }}</strong>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="password_confirmation">Confirm password <strong class="text-danger">*</strong></label>
                                        <input class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" type="password">
                                        @error('password_confirmation')
                                        <strong class="text-danger">{{ $errors->first('password_confirmation') }}</strong>
                                        @enderror
                                    </div>
                                    <input class="btn btn-primary" type="submit" value="Submit">
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
