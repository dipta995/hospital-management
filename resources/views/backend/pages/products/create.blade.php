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
                                            <x-default.label required="true" for="category_id">Category</x-default.label>
                                            <select class="form-control" name="category_id" id="category_id">
                                                <option value="">--Choose--</option>
                                                @foreach($categories as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                            <x-default.input-error name="category_id"></x-default.input-error>
                                        </div>
                                    <div class="form-group">
                                        <x-default.label required="true" for="name">Name</x-default.label>
                                        <x-default.input name="name" class="form-control" id="name" type="text"></x-default.input>
                                        <x-default.input-error name="name"></x-default.input-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="price">Price <strong
                                                class="text-danger">*</strong></label>
                                        <input id="price"
                                               class="form-control"
                                               name="price" type="number"
                                               value="{{ old('price') }}">
                                        @error('price')
                                        <strong class="text-danger">{{ $errors->first('price') }}</strong>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="code">Code</label>
                                        <input id="code"
                                               class="form-control"
                                               name="code" type="number"
                                               value="{{ old('code',$nextCode) }}">
                                        @error('code')
                                        <strong class="text-danger">{{ $errors->first('code') }}</strong>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="reefer_fee">Reefer Fee <strong
                                                class="text-danger">*</strong></label>
                                        <input id="reefer_fee"
                                               class="form-control"
                                               name="reefer_fee" type="number"
                                               value="{{ old('reefer_fee') }}">
                                        @error('reefer_fee')
                                        <strong class="text-danger">{{ $errors->first('reffer_fee') }}</strong>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <input id="description"
                                               class="form-control"
                                               name="description" type="text" value="{{ old('description') }}">
                                        @error('description')
                                        <strong class="text-danger">{{ $errors->first('description') }}</strong>
                                        @enderror
                                    </div>

                                    <x-default.button class="float-end mt-2 btn-success">Create</x-default.button>

                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- partial -->
    </div>
@endsection

@push('scripts')
    <script>
        $('#description').summernote({
            tabsize: 2,
            height: 400,
        })
    </script>
@endpush
