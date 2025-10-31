@extends('backend.layouts.master')
@section('title')
    Create New {{ $pageHeader['title'] }}
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
                            <h4 class="card-title">Create New {{ $pageHeader['title'] }}</h4>
                            @include('backend.layouts.partials.message')
                            <form class="cmxform" method="post" action="{{ route($pageHeader['store_route']) }}">
                                @csrf
                                <fieldset class="row">
                                    <div class="form-group col-md-6">
                                        <div class="form-group">
                                            <x-default.label required="true" for="cost_category_id">Category</x-default.label>
                                            <select class="form-control" name="cost_category_id" id="cost_category_id">
                                                <option value="">--Choose--</option>
                                                @foreach($categories as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                            <x-default.input-error name="cost_category_id"></x-default.input-error>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <x-default.label required="true" for="reason">Reason</x-default.label>
                                        <x-default.input name="reason" class="form-control" id="reason"
                                                         type="text"></x-default.input>
                                        <x-default.input-error name="reason"></x-default.input-error>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <x-default.label required="true" for="amount">Amount</x-default.label>
                                        <x-default.input name="amount" class="form-control" id="amount"
                                                         type="number"></x-default.input>
                                        <x-default.input-error name="amount"></x-default.input-error>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <x-default.label required="true" for="amount">Date</x-default.label>
                                        <x-default.input name="date" class="form-control" id="date"
                                                         value="{{ \Carbon\Carbon::now('Asia/Dhaka')->format('Y-m-d') }}"
                                                         type="date"></x-default.input>
                                        <x-default.input-error name="date"></x-default.input-error>
                                    </div>

                                    <x-default.button class="float-end mt-2 btn-success">Create</x-default.button>

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
