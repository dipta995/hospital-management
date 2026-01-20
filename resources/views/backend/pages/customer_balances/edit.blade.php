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
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <x-default.label for="patient">Patient</x-default.label>
                                        <input type="text" id="patient" class="form-control" value="{{ $edited->user->name ?? '' }} - {{ $edited->user->phone ?? '' }}" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <x-default.label required="true" for="balance">Balance Amount</x-default.label>
                                        <x-default.input name="balance" class="form-control" id="balance" type="number" step="0.01" value="{{ old('balance', $edited->balance) }}"/>
                                        <x-default.input-error name="balance"/>
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
