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
                                <x-default.label required="true" for="user_id">User ID</x-default.label>
                                <x-default.input name="user_id" class="form-control" id="user_id" type="number"/>
                                <x-default.input-error name="user_id"/>
                            </div>
                            <div class="form-group">
                                <x-default.label required="true" for="branch_id">Branch ID</x-default.label>
                                <x-default.input name="branch_id" class="form-control" id="branch_id" type="number" value="{{ auth()->user()->branch_id }}" readonly/>
                                <x-default.input-error name="branch_id"/>
                            </div>
                            <div class="form-group">
                                <x-default.label required="true" for="created_date">Created Date</x-default.label>
                                <x-default.input name="created_date" class="form-control" id="created_date" type="date"/>
                                <x-default.input-error name="created_date"/>
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
