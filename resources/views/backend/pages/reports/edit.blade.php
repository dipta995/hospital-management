@extends('backend.layouts.master')
@section('title')
    List of Account
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
                                    <ul>
                                        <li><strong>Invoice:</strong> {{ $edited->invoice->invoice_number }}</li>
                                        <li><strong>Test Name:</strong> {{ $edited->product->name }}</li>
                                        <li><strong>Patient:</strong> {{ $edited->invoice->patient_name }}</li>
                                    </ul>
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
    <script>
        $('#description').summernote({
            tabsize: 2,
            height: 120,
        })
    </script>
@endpush
