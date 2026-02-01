@extends('backend.layouts.master')
@section('title')
    Preview {{ $pageHeader['title'] }}
@endsection
@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet"/>

@endpush
@section('admin-content')
    <!-- partial -->
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">

                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">{{ $pageHeader['title'] }}'s Details</h4>
                            <div class="row">

                            </div>
                            <p class="card-description">
                                @include('backend.layouts.partials.message')
                            </p>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>Field</th>
                                        <th>Details</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <th>Invoice:</th>
                                        <td>{{ $singleData->invoice_number }} || (Patient Id
                                            -{{ $singleData->patient_no }})
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Patient Name:</th>
                                        <td>{{ $singleData->patient_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Patient Phone:</th>
                                        <td>{{ $singleData->patient_phone }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                                <a href="{{ route('admin.test_reports.create',['invoiceId='.$singleData->id]) }}"
                                   class="btn btn-info">+Add Test Report</a>
                                <table class="table table-striped mt-3">
                                    <thead>
                                    <tr>
                                        <th>Test Name</th>
                                        <th>price</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($singleData->invoiceList as $item)
                                        <tr>
                                            <td>{{ $item->product->name }}</td>
                                            <td>{{ $item->price }}</td>
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
                                                    @if($item->document != null)
                                                        <a class="badge bg-danger" target="_blank"
                                                           href="{{ route('admin.lab.report.file-download',$item->id) }}"><i
                                                                class="fas fa-download"></i></a>

                                                    @endif
                                                @endif
                                                @if($item->status==\App\Models\InvoiceList::$statusArray[2])
                                                    @if($item->test_report != null)
                                                        <a class="badge bg-info" target="_blank"
                                                           href="{{ route('admin.lab.report.pdf-preview',$item->id) }}"><i
                                                                class="fas fa-download"></i></a>
                                                    @endif
                                                @endif
                                                @if($item->status==\App\Models\InvoiceList::$statusArray[0] )
                                                    <div class="form-check form-switch">
                                                        <input onclick="statusUpdate({{ $item->id }},'/admin/labs')"
                                                               {{ $item->status==\App\Models\InvoiceList::$statusArray[1] ? 'checked' : '' }}
                                                               class="form-check-input" name="" type="checkbox"
                                                               id="status_switch{{ $item->id }}">
                                                    </div>
                                                @endif
                                                @if($item->status==\App\Models\InvoiceList::$statusArray[1])
                                                    <div class="form-check form-switch">
                                                        <input onclick="statusUpdate({{ $item->id }},'/admin/labs')"
                                                               {{ $item->status==\App\Models\InvoiceList::$statusArray[2] ? 'checked' : '' }}
                                                               class="form-check-input" name="" type="checkbox"
                                                               id="status_switch{{ $item->id }}">
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <form method="post" class="form-group"
                                                                  action="{{ route('admin.lab.update-item',$item->id) }}">
                                                                @csrf
                                                                <select class="form-control" name="items[]"
                                                                        id="select2{{$item->id}}" multiple>
                                                                    @foreach($purchaseItems as $pItem)
                                                                        <option
                                                                            value="{{ $pItem->id }}">{{ $pItem->item->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <button class="badge bg-info" type="submit">Save
                                                                </button>
                                                            </form>

                                                            <script>
                                                                $(document).ready(function () {

                                                                    $('#select2{{$item->id}}').select2({
                                                                        placeholder: "Select reagents",
                                                                        allowClear: true
                                                                    });
                                                                });
                                                            </script>

                                                        </div>
                                                        <div class="col-md-6">
                                                            {!! reAgents($item->id) !!}
                                                        </div>
                                                    </div>

                                                @endif

                                                @if($item->status==\App\Models\InvoiceList::$statusArray[2])
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <form method="post" class="form-group"
                                                                  action="{{ route('admin.lab.update-item',$item->id) }}">
                                                                @csrf
                                                                <select class="form-control" name="items[]"
                                                                        id="select2{{$item->id}}" multiple>
                                                                    @foreach($purchaseItems as $pItem)
                                                                        <option
                                                                            value="{{ $pItem->id }}">{{ $pItem->item->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <button class="badge bg-info" type="submit">Save
                                                                </button>
                                                            </form>

                                                            <script>
                                                                $(document).ready(function () {

                                                                    $('#select2{{$item->id}}').select2({
                                                                        placeholder: "Select reagents",
                                                                        allowClear: true
                                                                    });
                                                                });
                                                            </script>

                                                        </div>
                                                        <div class="col-md-6">
                                                            {!! reAgents($item->id) !!}
                                                        </div>
                                                    </div>

                                                @endif
                                            </td>
                                            <td>

                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                {{--                                Test Report For Download --}}
                                <table class="table table-striped mt-3">
                                    <thead>
                                    <tr>
                                        <th>Test Name</th>
                                        <th>Export</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($reports as $item)
                                        <tr>
                                            <td>{{ optional($item->invoiceItem->product)->name }}</td>
                                            <td>
                                                <a class="badge bg-danger" target="_blank"
                                                   href="{{ route('admin.preview-pdf-report',$item->id) }}"><i
                                                        class="fas fa-download"></i></a>
                                                <a class="badge bg-info"
                                                   href="{{ route('admin.test_reports.edit',$item->id) }}"><i
                                                        class="fas fa-pen"></i></a>
                                                <a class="badge bg-danger"
                                                   href="{{ route('admin.preview-pdf-delete',$item->id) }}"><i
                                                        class="fas fa-trash"></i></a>
                                            </td>

                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>


                                {{--                                Test Report For Download --}}


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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

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
