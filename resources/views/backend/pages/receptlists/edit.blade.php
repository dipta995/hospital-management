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
                        <h4 class="card-title">Edit Recept List #{{ $edited->id }}</h4>
                        @include('backend.layouts.partials.message')
                        <form method="POST" action="{{ route($pageHeader['update_route'], $edited->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="user_id">User ID <strong class="text-danger">*</strong></label>
                                <input type="number" id="user_id" name="user_id" class="form-control" value="{{ old('user_id', $edited->user_id) }}">
                            </div>
                            <div class="form-group">
                                <label for="recept_id">Recept ID <strong class="text-danger">*</strong></label>
                                <input type="number" id="recept_id" name="recept_id" class="form-control" value="{{ old('recept_id', $edited->recept_id) }}">
                            </div>
                            <div class="form-group">
                                <label for="service_id">Service ID <strong class="text-danger">*</strong></label>
                                <input type="number" id="service_id" name="service_id" class="form-control" value="{{ old('service_id', $edited->service_id) }}">
                            </div>
                            <div class="form-group">
                                <label for="price">Price <strong class="text-danger">*</strong></label>
                                <input type="number" id="price" name="price" class="form-control" step="0.01" value="{{ old('price', $edited->price) }}">
                            </div>
                            <div class="form-group">
                                <label for="discount">Discount</label>
                                <input type="number" id="discount" name="discount" class="form-control" step="0.01" value="{{ old('discount', $edited->discount) }}">
                            </div>
                            <div class="form-group">
                                <label for="amount">Amount <strong class="text-danger">*</strong></label>
                                <input type="number" id="amount" name="amount" class="form-control" step="0.01" value="{{ old('amount', $edited->amount) }}">
                            </div>
                            <div class="form-group">
                                <label for="branch_id">Branch ID <strong class="text-danger">*</strong></label>
                                <input type="number" id="branch_id" name="branch_id" class="form-control" value="{{ old('branch_id', $edited->branch_id) }}" readonly>
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
