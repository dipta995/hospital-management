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

                            <form class="cmxform" method="post" action="{{ route($pageHeader['update_route'],[$edited->id]). '?status=' . request('status') }}" enctype="multipart/form-data">
                                @method('PUT')
                                @csrf
                                <fieldset>
                                    <ul>
                                        <li><strong>Invoice:</strong> {{ $edited->invoice->invoice_number }}</li>
                                        <li><strong>Test Name:</strong> {{ $edited->product->name }}</li>
                                        <li><strong>Patient:</strong> {{ $edited->invoice->patient_name }}</li>
                                    </ul>
                                    <div class="form-group">
                                        <label for="file">Document File</label>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="file" name="file" id="file" class="form-control">
                                                @if(isset($edited['file']))
                                                    <img src="{{ asset('images/' . $edited['document']) }}" alt="Logo"
                                                         style="max-height: 100px;">
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                @if($edited->document != null)
                                                    <a class="btn btn-info" target="_blank"
                                                       href="{{ route('admin.lab.report.file-download',$edited->id) }}"><i
                                                            class="fas fa-download"></i></a>
                                                @else
                                                    <span class="btn btn-danger">No File Available</span>
                                                @endif
                                            </div>
                                        </div>


                                    </div>
                                    <div class="form-group">
                                        <label for="description">Report Edit <strong class="text-danger">*</strong></label>
                                        <textarea name="description" id="description" cols="30" rows="10">{!! $edited->test_report ?? $edited->product->description   !!} </textarea>
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
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#description'), {
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
