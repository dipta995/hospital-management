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
                            <div class="form-group">
                                <label for="name">Name <strong class="text-danger">*</strong></label>
                                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $edited->name) }}">
                            </div>
                            <div class="form-group">
                                <label for="price">Price <strong class="text-danger">*</strong></label>
                                <input type="number" id="price" name="price" class="form-control" step="0.01" value="{{ old('price', $edited->price) }}">
                            </div>
                            <div class="form-group">
                                <label for="note">Note</label>
                                <textarea id="note" name="note" class="form-control" rows="3">{{ old('note', $edited->note) }}</textarea>
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
