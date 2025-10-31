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
                                        <x-default.label required="true" for="reefer_id">Doctor Name</x-default.label>
                                        <select class="form-control" name="reefer_id" id="reefer_id">
                                            <option value="">--Choose--</option>
                                            @foreach($reefers as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-default.input-error name="reefer_id"></x-default.input-error>
                                    </div>
                                    <div class="form-group">
                                        <x-default.label required="true" for="room_no">Room name</x-default.label>
                                        <x-default.input name="room_no" class="form-control" id="room_no"
                                                         type="text"></x-default.input>
                                        <x-default.input-error name="room_no"></x-default.input-error>
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
