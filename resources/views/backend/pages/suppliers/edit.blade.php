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

                            <form  method="post" action="{{ route($pageHeader['update_route'], $edited->id) }}">
                                @method('PUT')
                                @csrf
                                <fieldset>
                                    <div class="form-group">
                                        <x-default.label required="true" for="name">Name</x-default.label>
                                        <x-default.input name="name" class="form-control" id="name"
                                                         value="{{ old('name',$edited->name) }}"   type="text"></x-default.input>
                                        <x-default.input-error name="name"></x-default.input-error>
                                    </div>
                                    <div class="form-group">
                                        <x-default.label required="true" for="contact_person">Contact Person
                                        </x-default.label>
                                        <x-default.input name="contact_person" class="form-control" id="contact_person"
                                                         value="{{ old('name',$edited->contact_person) }}"    type="text"></x-default.input>
                                        <x-default.input-error name="contact_person"></x-default.input-error>
                                    </div>

                                    <div class="form-group">
                                        <label for="phone">Phone</label>
                                        <input id="phone"
                                               class="form-control"
                                               name="phone" type="text" value="{{ old('phone',$edited->phone) }}">
                                        @error('phone')
                                        <strong class="text-danger">{{ $errors->first('phone') }}</strong>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input id="email"
                                               class="form-control"
                                               name="email" type="email" value="{{ old('email',$edited->email) }}">
                                        @error('email')
                                        <strong class="text-danger">{{ $errors->first('email') }}</strong>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <input id="address"
                                               class="form-control"
                                               name="address" type="text" value="{{ old('address',$edited->address) }}">
                                        @error('address')
                                        <strong class="text-danger">{{ $errors->first('address') }}</strong>
                                        @enderror
                                    </div>
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

@endpush
