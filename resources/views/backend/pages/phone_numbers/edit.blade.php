@extends('backend.layouts.master')
@section('title')
    List of {{ $pageHeader['title'] }}
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
                                    <div class="form-group col-md-4">
                                        <x-default.label required="true" for="number_category_id">Category</x-default.label>
                                        <select class="form-control" name="number_category_id" id="number_category_id">
                                            <option value="">--Choose--</option>
                                            @foreach($numberCategories as $item)
                                                <option @selected(old('number_category_id', $edited->number_category_id) == $item->id) value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-default.input-error name="number_category_id"></x-default.input-error>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <x-default.label required="true" for="name">Name</x-default.label>
                                        <x-default.input name="name" value="{{ old('name',$edited->name) }}" class="form-control" id="name" type="text"></x-default.input>
                                        <x-default.input-error name="name"></x-default.input-error>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <x-default.label required="true" for="number">Phone Number</x-default.label>
                                        <x-default.input name="number"  value="{{ old('number',$edited->number) }}" class="form-control" id="number" type="text"></x-default.input>
                                        <x-default.input-error name="number"></x-default.input-error>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <x-default.label required="false" for="address">Address</x-default.label>
                                        <x-default.input name="address"  value="{{ old('address',$edited->address) }}" class="form-control" id="address" type="text"></x-default.input>
                                        <x-default.input-error name="address"></x-default.input-error>
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
