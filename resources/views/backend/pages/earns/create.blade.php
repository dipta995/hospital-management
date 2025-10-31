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
                                <fieldset>
                                    <div class="form-group">
                                        <x-default.label required="true" for="name">Name</x-default.label>
                                        <x-default.input name="name" class="form-control" id="name" type="text"></x-default.input>
                                        <x-default.input-error name="name"></x-default.input-error>
                                    </div>
                                    <div class="form-group">
                                        <x-default.label  required="true" for="type">Type</x-default.label>
                                        <select  class="form-control" name="type" id="">
                                            <option value="Loan">Loan</option>
                                            <option value="Deposit">Deposit</option>
                                            <option value="Advance From Shareholder">Advance From Shareholder</option>
                                            <option value="Withdrew">Withdrew</option>
                                            <option value="Other">Other</option>
                                        </select>
                                        <x-default.input-error name="type"></x-default.input-error>
                                    </div>
                                    <div class="form-group">
                                        <x-default.label required="true" for="amount">Amount</x-default.label>
                                        <x-default.input name="amount" class="form-control" id="amount"
                                                         type="number"></x-default.input>
                                        <x-default.input-error name="amount"></x-default.input-error>
                                    </div>
                                    <div class="form-group">
                                        <x-default.label required="true" for="date">Date</x-default.label>
                                        <x-default.input name="date" class="form-control" id="date"
                                                         type="date"></x-default.input>
                                        <x-default.input-error name="date"></x-default.input-error>
                                    </div>
                                    <div class="form-group">
                                        <x-default.label required="true" for="date">note</x-default.label>
                                        <textarea class="form-control" name="note" id="" cols="30" rows="10"></textarea>
                                        <x-default.input-error name="note"></x-default.input-error>
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
