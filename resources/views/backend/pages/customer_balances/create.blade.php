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
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <x-default.label required="true" for="user_id">Patient</x-default.label>
                                        <select name="user_id" id="user_id" class="form-control">
                                            <option value="">Select Patient</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ old('user_id', request('user_id')) == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }} - {{ $user->phone }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-default.input-error name="user_id"/>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <x-default.label required="true" for="balance">Balance Amount</x-default.label>
                                        <x-default.input name="balance" class="form-control" id="balance" type="number" step="0.01" value="{{ old('balance') }}"/>
                                        <x-default.input-error name="balance"/>
                                    </div>
                                </div>
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
