@extends('backend.layouts.master')
@section('title')
    List of {{ $pageHeader['title'] }}
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
                            <h4 class="card-title">Modify  <strong>{{ $edited->name }}'s</strong> Information</h4>
                            @include('backend.layouts.partials.message')

                            <form class="cmxform" method="post" action="{{ route($pageHeader['update_route'], $edited->id) }}">
                                @method('PUT')
                                @csrf
                                <fieldset>
                                    <div class="form-group">
                                        <div class="form-group">
                                            <x-default.label required="true" for="cost_category_id">Category</x-default.label>
                                            <select class="form-control" name="cost_category_id" id="cost_category_id">
                                                <option value="">--Choose--</option>
                                                @foreach($categories as $item)
                                                    <option @selected(old('cost_category_id', $edited->cost_category_id) == $item->id)  value="{{ $item->id }}">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                            <x-default.input-error name="cost_category_id"></x-default.input-error>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <x-default.label required="true" for="reason">Reason</x-default.label>
                                        <x-default.input name="reason" class="form-control" value="{{ old('name',$edited->reason) }}" id="reason"
                                                         type="text"></x-default.input>
                                        <x-default.input-error name="reason"></x-default.input-error>
                                    </div>
                                    <div class="form-group">
                                        <x-default.label required="true" for="amount">Amount</x-default.label>
                                        <x-default.input name="amount" class="form-control" value="{{ old('name',$edited->amount) }}" id="amount"
                                                         type="number"></x-default.input>
                                        <x-default.input-error name="amount"></x-default.input-error>
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
