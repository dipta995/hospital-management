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
                                    <input type="hidden" name="invoiceId" value="{{ $invoiceId }}">
                                    <input type="hidden" name="testReport" value="{{ $testReport }}">

                                    <div class="form-group">
                                        <x-default.label required="true" for="name">Name</x-default.label>
                                        <select name="" class="form-control" id="demo_id">
                                            <option value="">choose test report</option>
                                            @foreach($reportDemo as $item)
                                                <option  value="{{ $item->id }}" @selected(request('testReport') == $item->id)>{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-default.input-error name="name"></x-default.input-error>
                                    </div>

                                    @if(request()->has('testReport'))
                                        <div class="form-group">
                                            <x-default.label required="true" for="report">Test Report</x-default.label>
                                            <textarea name="report" id="report" class="form-control" cols="30" rows="10">{{ $edited->test_report }}</textarea>
                                            <x-default.input-error name="report"></x-default.input-error>
                                        </div>
                                    @endif


                                    <x-default.button class="float-end mt-2 btn-success">Create</x-default.button>
                                </fieldset>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#report'), {
                toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'undo', 'redo' ]
            })
            .then(editor => {
                // Load initial content

            })
            .catch(error => {
                console.error(error);
            });
        $(document).ready(function () {
            $('#demo_id').on('change', function () {
                const selectedId = $(this).val();

                const url = new URL(window.location.href);
                if (selectedId) {
                    url.searchParams.set('testReport', selectedId);
                } else {
                    url.searchParams.delete('testReport');
                }

                window.location.href = url.toString();
            });
        });

    </script>
@endpush
