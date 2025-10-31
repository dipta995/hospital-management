@extends('backend.layouts.master')
@section('title')
    Edit {{ $pageHeader['title'] }}
@endsection

@push('styles')

@endpush

@section('admin-content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Edit {{ $pageHeader['title'] }}</h4>
                            @include('backend.layouts.partials.message')

                            <a href="{{ route($pageHeader['index_route']) }}" class="btn btn-secondary mb-3 float-end">Back to List</a>

                            <form class="cmxform" method="post" action="{{ route($pageHeader['update_route'], $data->id) }}">
                                @csrf
                                @method('PUT')
                                <fieldset>
                                    <div class="form-group">
                                        <x-default.label required="true" for="name">Name</x-default.label>
                                        <x-default.input name="name" class="form-control" id="name" type="text"
                                                         value="{{ old('name', $data->name) }}"></x-default.input>
                                        <x-default.input-error name="name"></x-default.input-error>
                                    </div>

                                    <div class="form-group">
                                        <x-default.label required="true" for="type">Type</x-default.label>
                                        <select class="form-control" name="type" id="type">
                                            <option value="Loan" {{ old('type', $data->type) == 'Loan' ? 'selected' : '' }}>Loan</option>
                                            <option value="Deposit" {{ old('type', $data->type) == 'Deposit' ? 'selected' : '' }}>Deposit</option>
                                            <option value="Advance From Shareholder" {{ old('type', $data->type) == 'Advance From Shareholder' ? 'selected' : '' }}>Advance From Shareholder</option>
                                            <option value="Withdrew" {{ old('type', $data->type) == 'Withdrew' ? 'selected' : '' }}>Withdrew</option>
                                            <option value="Other" {{ old('type', $data->type) == 'Other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        <x-default.input-error name="type"></x-default.input-error>
                                    </div>

                                    <div class="form-group">
                                        <x-default.label required="true" for="amount">Amount</x-default.label>
                                        <x-default.input name="amount" class="form-control" id="amount" type="number"
                                                         value="{{ old('amount', $data->amount) }}"></x-default.input>
                                        <x-default.input-error name="amount"></x-default.input-error>
                                    </div>

                                    <div class="form-group">
                                        <x-default.label required="true" for="date">Date</x-default.label>
                                        <x-default.input name="date" class="form-control" id="date" type="date"
                                                         value="{{ old('date', $data->date) }}"></x-default.input>
                                        <x-default.input-error name="date"></x-default.input-error>
                                    </div>

                                    <div class="form-group">
                                        <x-default.label required="true" for="note">Note</x-default.label>
                                        <textarea class="form-control" name="note" id="note" cols="30" rows="10">{{ old('note', $data->note) }}</textarea>
                                        <x-default.input-error name="note"></x-default.input-error>
                                    </div>

                                    <x-default.button class="float-end mt-2 btn-primary">Update</x-default.button>

                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')

@endpush
