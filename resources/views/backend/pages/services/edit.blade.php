@extends('backend.layouts.master')
@section('title')
    Edit {{ $pageHeader['title'] }}
@endsection
@section('admin-content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit {{ $edited->name }}</h4>
                        @include('backend.layouts.partials.message')

                        <form method="POST" action="{{ route($pageHeader['update_route'], $edited->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <x-default.label required="true" for="service_category_id">Category</x-default.label>
                                        <select class="form-control" name="service_category_id" id="service_category_id">
                                            <option value="">--Choose--</option>
                                            @foreach($categories as $item)
                                                <option value="{{ $item->id }}" {{ old('service_category_id', $edited->service_category_id) == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-default.input-error name="service_category_id"></x-default.input-error>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <x-default.label required="true" for="name">Name</x-default.label>
                                        <x-default.input name="name" class="form-control" id="name" type="text" value="{{ old('name', $edited->name) }}"/>
                                        <x-default.input-error name="name"/>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <x-default.label required="true" for="price">Price</x-default.label>
                                        <x-default.input name="price" class="form-control" id="price" type="number" step="0.01" value="{{ old('price', $edited->price) }}"/>
                                        <x-default.input-error name="price"/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <x-default.label for="note">Note</x-default.label>
                                        <textarea id="note" name="note" class="form-control" rows="3">{{ old('note', $edited->note) }}</textarea>
                                        <x-default.input-error name="note"/>
                                    </div>
                                </div>
                            </div>

                            <x-default.button class="float-end mt-2 btn-success">Update</x-default.button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
