@extends('backend.layouts.master')
@section('title')
    List of {{ $pageHeader['title'] }}'s
@endsection
@push('styles')

@endpush
@section('admin-content')
    <!-- partial -->
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">

                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">{{ $pageHeader['title'] }}'s List</h4>
                            <form action="" method="GET" class="mb-4">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="start_date">Start Date:</label>
                                        <input type="date" name="start_date" id="start_date" class="form-control"
                                               value="{{ request('start_date') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="end_date">End Date:</label>
                                        <input type="date" name="end_date" id="end_date" class="form-control"
                                               value="{{ request('end_date') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="end_date">Invoice:</label>
                                        <input type="text" name="invoice_number"  id="invoice_number"
                                               class="form-control"
                                               value="{{ request('invoice_number') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="start_date">Status:</label>
                                        <select class="form-control" name="status" id="">
                                            <option value="">Choose</option>
                                            @foreach(\App\Models\InvoiceList::$statusArray as $item)
                                            <option @selected($item == request('status')) value="{{ $item }}">{{ $item }}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <a href="{{ route('admin.labs.index') }}" class="btn btn-secondary ms-2">Reset</a>
                                    </div>
                                </div>
                            </form>
                            <p class="card-description">
                                @include('backend.layouts.partials.message')
                            </p>
                            <div class="table-responsive">
                                <table class="table table-striped mt-3">
                                    <thead>
                                    <tr>
                                        <th>Test Name</th>
                                        <th>Id | Invoice</th>
                                        <th>Patient Name</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($datas as $item)
                                        <tr>
                                            <td>{{ $item->product->name }}</td>
                                            <td>{{ $item->invoice->patient_no }} | {{ $item->invoice->invoice_number }}</td>
                                            <td>{{ $item->invoice->patient_name }}</td>
                                            <td>
                                                <span class="badge
                                                 @if($item->status==\App\Models\InvoiceList::$statusArray[0])
                                                 bg-danger
                                                 @elseif($item->status==\App\Models\InvoiceList::$statusArray[1])
                                                bg-warning
                                                 @elseif($item->status==\App\Models\InvoiceList::$statusArray[2])
                                                 bg-info
                                                 @else
                                                 bg-dark
                                                @endif
                                                ">
                                                    {{ $item->status }}
                                                </span>
                                                @if($item->status==\App\Models\InvoiceList::$statusArray[2])
{{--                                                    <a class="badge bg-danger" target="_blank"--}}
{{--                                                       href="{{ route('admin.lab.report.pdf-preview',$item->id) }}"><i--}}
{{--                                                            class="fas fa-file-pdf"></i></a>   --}}
{{--                                                    --}}
                                                @if($item->document != null)
                                                    <a class="badge bg-danger" target="_blank"
                                                       href="{{ route('admin.lab.report.file-download',$item->id) }}"><i
                                                            class="fas fa-download"></i></a>
                                                    @endif
                                                        <a class="badge bg-info"
                                                           href="{{ route('admin.labs.edit',[$item->id]). '?status=' . request('status') }}"><i
                                                                class="fas fa-pencil"></i></a>
                                                @endif
                                                <div class="row">
                                                    <div class="col-md-2">

                                                @if($item->status==\App\Models\InvoiceList::$statusArray[0] || $item->status==\App\Models\InvoiceList::$statusArray[1])
                                                    <div class="form-check form-switch">
                                                        <input onclick="statusUpdate({{ $item->id }},'/admin/labs')"
                                                               {{ $item->status==\App\Models\InvoiceList::$statusArray[1] ? 'checked' : '' }}
                                                               class="form-check-input" name="" type="checkbox"
                                                               id="status_switch{{ $item->id }}">
                                                    </div>
                                                @endif
                                                    </div>
                                                    <div class="col-md-2">
                                                @if($item->status==\App\Models\InvoiceList::$statusArray[1])
                                                    <a class="badge bg-info"
                                                       href="{{ route('admin.labs.edit',$item->id) }}"><i
                                                                class="fas fa-pencil"></i></a>
                                                @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-end">
                                    {!! $datas->appends(request()->query())->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
    <!-- main-panel ends -->
@endsection

@push('scripts')
    <script !src="">
        function statusUpdate(id, url_base_name) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })
            $.ajax({
                url: url_base_name + "/status/" + id,
                type: "GET",
                data: {
                    _token: $("input[name=_token]").val()
                },
                success: function (response) {

                    Toast.fire({
                        icon: 'success',
                        title: 'Successo !'
                    })
                    location.reload();
                },
                error: function (response) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Opps! Qualcosa non ha funzionato.'
                    })
                },

            });


        }
    </script>
@endpush
