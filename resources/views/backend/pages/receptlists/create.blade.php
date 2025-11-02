@extends('backend.layouts.master')

@section('title')
    Create New {{ $pageHeader['title'] }}
@endsection

@section('admin-content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Create New {{ $pageHeader['title'] }}</h4>

                        @include('backend.layouts.partials.message')

                        <form method="POST" action="{{ route($pageHeader['store_route']) }}">
                            @csrf

                            <div class="form-group">
                                <x-default.label required="true" for="recept_id">Recept ID</x-default.label>
                                <x-default.input name="recept_id" id="recept_id" class="form-control"
                                                 type="number" value="{{ $recept_id ?? '' }}" readonly />
                                <x-default.input-error name="recept_id" />
                            </div>

                            <div class="form-group">
                                <x-default.label required="true" for="user_id">User ID</x-default.label>
                                <x-default.input name="user_id" id="user_id" class="form-control"
                                                 type="number" value="{{ auth()->user()->id }}" readonly />
                                <x-default.input-error name="user_id" />
                            </div>

                            <div class="form-group">
                                <x-default.label required="true" for="service_id">Service ID</x-default.label>
                                <x-default.input name="service_id" id="service_id" class="form-control"
                                                 type="number" />
                                <x-default.input-error name="service_id" />
                            </div>

                            <div class="form-group">
                                <x-default.label required="true" for="price">Price</x-default.label>
                                <x-default.input name="price" id="price" class="form-control"
                                                 type="number" step="0.01" />
                                <x-default.input-error name="price" />
                            </div>

                            <div class="form-group">
                                <x-default.label for="discount">Discount</x-default.label>
                                <x-default.input name="discount" id="discount" class="form-control"
                                                 type="number" step="0.01" />
                                <x-default.input-error name="discount" />
                            </div>

                            <div class="form-group">
                                <x-default.label required="true" for="amount">Amount</x-default.label>
                                <x-default.input name="amount" id="amount" class="form-control"
                                                 type="number" step="0.01" />
                                <x-default.input-error name="amount" />
                            </div>

                            <div class="form-group">
                                <x-default.label required="true" for="branch_id">Branch ID</x-default.label>
                                <x-default.input name="branch_id" id="branch_id" class="form-control"
                                                 type="number" value="{{ auth()->user()->branch_id }}" readonly />
                                <x-default.input-error name="branch_id" />
                            </div>

                            <x-default.button class="float-end mt-2 btn-success">Create</x-default.button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
