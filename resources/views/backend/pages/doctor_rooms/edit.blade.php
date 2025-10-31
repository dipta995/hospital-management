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
                                        <label for="room_no">Room<strong
                                                class="text-danger">*</strong></label>
                                        <input id="room_no"
                                               class="form-control @error('room_no') is-invalid @enderror"
                                               name="room_no" type="number"
                                               value="{{ old('rice',$edited->room_no) }}">
                                        @error('room_no')
                                        <strong class="text-danger">{{ $errors->first('room_no') }}</strong>
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
