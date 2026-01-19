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
                        <h4 class="card-title">Edit {{ $pageHeader['title'] }}</h4>
                        @include('backend.layouts.partials.message')
                        <form method="POST" action="{{ route($pageHeader['update_route'], $edited->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <x-default.label required="true" for="name">Bed/Cabin Name or Number</x-default.label>
                                        <x-default.input name="name" class="form-control" id="name" type="text" value="{{ old('name', $edited->name) }}"/>
                                        <x-default.input-error name="name"/>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <x-default.label required="true" for="type">Type</x-default.label>
                                        <select name="type" id="type" class="form-control">
                                            <option value="bed" {{ old('type', $edited->type) == 'bed' ? 'selected' : '' }}>Bed</option>
                                            <option value="cabin" {{ old('type', $edited->type) == 'cabin' ? 'selected' : '' }}>Cabin</option>
                                        </select>
                                        <x-default.input-error name="type"/>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <x-default.label required="true" for="status">Status</x-default.label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="available" {{ old('status', $edited->status) == 'available' ? 'selected' : '' }}>Available</option>
                                            <option value="occupied" {{ old('status', $edited->status) == 'occupied' ? 'selected' : '' }}>Occupied</option>
                                            <option value="maintenance" {{ old('status', $edited->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        </select>
                                        <x-default.input-error name="status"/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <x-default.label for="price">Price (Per Day)</x-default.label>
                                        <x-default.input name="price" class="form-control" id="price" type="number" step="0.01" value="{{ old('price', $edited->price) }}"/>
                                        <x-default.input-error name="price"/>
                                    </div>
                                </div>

                                <div class="col-md-8 mb-3">
                                    <div class="form-group">
                                        <x-default.label for="note">Note</x-default.label>
                                        <textarea name="note" id="note" class="form-control" rows="2">{{ old('note', $edited->note) }}</textarea>
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
