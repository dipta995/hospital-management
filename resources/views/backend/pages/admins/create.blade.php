@extends('backend.layouts.master')
@section('title')
    Create New Admin
@endsection
@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

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
                                <fieldset class="row">
                                    <div class="form-group col-md-6">
                                        <label for="name">Name <strong class="text-danger">*</strong></label>
                                        <input id="name" class="form-control @error('name') is-invalid @enderror" name="name" type="text" value="{{ old('name') }}">
                                        @error('name')
                                        <strong class="text-danger">{{ $errors->first('name') }}</strong>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="username">Username <strong class="text-danger">*</strong></label>
                                        <input id="username" class="form-control @error('username') is-invalid @enderror" name="username" type="text" value="{{ old('username') }}">
                                        @error('username')
                                        <strong class="text-danger">{{ $errors->first('username') }}</strong>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="email">Email <strong class="text-danger">*</strong></label>
                                        <input id="email" class="form-control @error('email') is-invalid @enderror" name="email" type="email" value="{{ old('email') }}">
                                        @error('email')
                                        <strong class="text-danger">{{ $errors->first('email') }}</strong>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Assign Role's<strong class="text-danger">*</strong></label>
                                        <select class="form-control select3" id="roles" multiple name="roles[]" >
                                            <option value="">Choose Role</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Doctor<strong class="text-danger"></strong></label>
                                        <select class="form-control select4" id="reefer_id" name="reefer_id" >
                                            <option value="">Choose Doctor</option>
                                            @foreach ($reefers as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Branch<strong class="text-danger">*</strong></label>
                                        <select class="form-control" id="branch_id" name="branch_id" >
                                            <option value="">Choose branch</option>
                                            @foreach ($branches as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="password">Password <strong class="text-danger">*</strong></label>
                                        <input class="form-control @error('password') is-invalid @enderror" name="password" type="password">
                                        @error('password')
                                        <strong class="text-danger">{{ $errors->first('password') }}</strong>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="password_confirmation">Confirm password <strong class="text-danger">*</strong></label>
                                        <input class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" type="password">
                                        @error('password_confirmation')
                                        <strong class="text-danger">{{ $errors->first('password_confirmation') }}</strong>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Language<strong class="text-danger">*</strong></label>
                                        <select class="form-control" id="language" name="language" >
                                            <option value="">Choose Language</option>
                                                <option value="en">English</option>
                                                <option value="bn">Bangle</option>
                                        </select>
                                    </div>
                                    @error('language')
                                    <strong class="text-danger">{{ $errors->first('language') }}</strong>
                                    @enderror
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2 on the elements
            $('.select4').select2({
                placeholder: "Select an option",
                allowClear: true // Allow clearing the selection
            });
            $('.select3').select2({
                placeholder: "Select an option",
                allowClear: true // Allow clearing the selection
            });
        });

    </script>
@endpush
