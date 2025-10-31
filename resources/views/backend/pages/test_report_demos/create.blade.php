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
                                        <x-default.label required="true" for="type">Type</x-default.label>
                                        <x-default.input name="type"  class="form-control" id="type" type="text"></x-default.input>
                                        <x-default.input-error name="type"></x-default.input-error>
                                    </div>
                                    <div class="form-group">
                                        <x-default.label required="true" for="test_report">Test Report</x-default.label>
                                        <textarea name="test_report" id="test_report" class="form-control" cols="30" rows="10"></textarea>
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
        <!-- content-wrapper ends -->
         <!-- partial -->
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#test_report'), {
                toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'undo', 'redo' ]
            })
            .then(editor => {
                // Load initial content

            })
            .catch(error => {
                console.error(error);
            });
    </script>
@endpush
