@extends('backend.layouts.master')
@section('title')
    Edit {{ $pageHeader['title'] }}
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
                                        <label for="name">Name <strong class="text-danger">*</strong></label>
                                        <input id="name" class="form-control "
                                               name="name" type="text" value="{{ old('name',$edited->name) }}">
                                        @error('name')
                                        <strong class="text-danger">{{ $errors->first('name') }}</strong>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="code">Code<strong
                                                class="text-danger">*</strong></label>
                                        <input id="code"
                                               class="form-control @error('code') is-invalid @enderror"
                                               name="code" type="number"
                                               value="{{ old('rice',$edited->code) }}">
                                        @error('code')
                                        <strong class="text-danger">{{ $errors->first('code') }}</strong>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="price">Price<strong
                                                class="text-danger">*</strong></label>
                                        <input id="price"
                                               class="form-control @error('price') is-invalid @enderror"
                                               name="price" type="number"
                                               value="{{ old('price',$edited->price) }}">
                                        @error('price')
                                        <strong class="text-danger">{{ $errors->first('price') }}</strong>
                                        @enderror
                                    </div>


                                    <div class="form-group">
                                        <label for="reefer_fee">Reefer Fee<strong
                                                class="text-danger">*</strong></label>
                                        <input id="reefer_fee"
                                               class="form-control @error('reefer_fee') is-invalid @enderror"
                                               name="reefer_fee" type="number"
                                               value="{{ old('reefer_fee',$edited->reefer_fee) }}">
                                        @error('reefer_fee')
                                        <strong class="text-danger">{{ $errors->first('reefer_fee') }}</strong>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea name="description" id="description" cols="30" rows="10">{{ old('description',$edited->description) }}</textarea>
                                        @error('description')
                                        <strong class="text-danger">{{ $errors->first('description') }}</strong>
                                        @enderror
                                    </div>

                                    <x-default.button class="float-end mt-2 btn-success">Update</x-default.button>
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
    <script>
        $('#description').summernote({
            tabsize: 2,
            height: 400,
        })
    </script>
@endpush
