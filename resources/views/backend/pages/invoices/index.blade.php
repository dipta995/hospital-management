@extends('backend.layouts.master')
@section('title')
    List of {{ $pageHeader['title'] }}
    @php
        $userGuard = Auth::guard('admin')->user();
    @endphp
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
                        <div class="card-head">
                            <div class="col-md-12">
                                <a href="{{ route('admin.users.index') }}" class="mr-10 float-end btn btn-danger">USER List</a>
                                <a href="{{ route('admin.users.create') }}" class="ml-5 float-end btn btn-dark">USER Create</a>
                            </div>
                            <br>
                        </div>
                        <div class="card-body">
                            <h4 class="card-title">{{ $pageHeader['title'] }}'s List</h4>

                            <p class="card-description">
                                @include('backend.layouts.partials.message')

                            </p>
                            <form action="{{ route('admin.invoices.index') }}" method="GET" class="mb-4">
                                <div class="row">
                                    @if (!$userGuard->can('invoices.desk'))
                                    <div class="col-md-2">
                                        <label for="start_date">Start Date:</label>
                                        <input type="date" name="start_date" id="start_date" class="form-control"
                                               value="{{ request('start_date') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="end_date">End Date:</label>
                                        <input type="date" name="end_date" id="end_date" class="form-control"
                                               value="{{ request('end_date') }}">
                                    </div>

                                    @endif
                                    <div class="col-md-3">
                                        <label for="start_date">Doctor:</label>
                                        <select class="form-control" name="dr_refer_id" id="select2">
                                            <option value="">Choose</option>
                                            @foreach($reffers as $item)
                                                <option
                                                    @selected(old('dr_refer_id', request('dr_refer_id')) == $item->id) value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="invoice_by" class="form-label">Invoice By:</label>
                                        <select name="admin_id" id="invoice_by" class="form-control form-control-sm">
                                            <option value="">Select</option>
                                            @foreach(\App\Models\Admin::where('branch_id',auth()->user()->branch_id)->get() as $item)
                                                <option
                                                    value="{{ $item->id }}" @selected(old('admin_id', request('admin_id')) == $item->id) >{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-1">
                                        <label for="due_filter" class="form-label">Due:</label>
                                        <select name="due" id="due_filter" class="form-control form-control-sm"
                                                onchange="this.form.submit()">
                                            <option value="">Select</option>
                                            <option value="yes" {{ request('due') == 'yes' ? 'selected' : '' }}>Yes
                                            </option>
                                            <option value="no" {{ request('due') == 'no' ? 'selected' : '' }}>No
                                            </option>
                                        </select>
                                    </div>


                                    <div class="col-md-2">
                                        <label for="end_date">Invoice:</label>
                                        <input type="text" name="invoice_number" id="invoice_number"
                                               class="form-control"
                                               value="{{ request('invoice_number') }}">
                                    </div>
                                    <div class="col-md-12 mt-2 d-flex justify-content-end align-items-end">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary ms-2">Reset</a>
                                    </div>

                                </div>
                            </form>
                            <div class="table-responsive">
                                <div class="row">
                                    <div class="text-start col-md-6">
                                    <span
                                        class="bg-danger text-white p-1 ">Own : <span>{{ $my_collection }} </span></span>
                                        | @if ( $userGuard->can('reports.amounts'))
                                            <span
                                                class="bg-warning text-white p-1 ">Others : <span>{{ $other_collection }} </span></span>
                                        @endif
                                    </div>
                                    @if ( $userGuard->can('reports.amounts'))
                                        <div class="text-end  col-md-6">
                                    <span
                                        class="bg-info text-white p-1 ">Collection : <span>{{ $total_paid_amount }} </span></span>
                                            |
                                            <span
                                                class="bg-success text-white p-1 ">Discount : <span>{{ $discount_amount }} </span></span>
                                            |
                                            <span
                                                class="bg-danger text-white p-1 "> Due : <span>{{ $total_due_amount }}</span></span>
                                        </div>
                                    @endif
                                    {{--                                        </p>--}}
                                    <table style="font-size: 13px;" class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name [Id]</th>
                                            <th>DR</th>
                                            <th>Reefer By</th>
                                            <th>Inv By</th>
                                            <th>Total</th>
                                            <th>Discount</th>
                                            <th>Paid</th>
                                            <th>Due</th>
                                            <th>LAB||Status</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($datas as $item)
                                            <tr id="table-data{{ $item->id }}">
                                                <td>{{ $loop->index + 1 }}. {{ $item->invoice_number }}</td>
                                                <td>{{ $item->patient_name ?? 'NA'}} <br> [{{ $item->patient_no }}]
                                                </td>
                                                <td>{{ $item->reeferDr->name ?? 'NA' }}</td>
                                                <td>{{ $item->reeferBy->name ?? 'NA' }}
                                                    ({{ $item->refer_fee_total - ($item->costs->sum('amount')) }})
                                                </td>
                                                <td>{{ $item->admin->name ?? 'NA' }}</td>
                                                <td>{{ $item->total_amount }}</td>
                                                <td>{{ $item->discount_amount }}</td>
                                                <td>{{ $item->paid_amount_sum_paid_amount ?? 0 }}</td>
                                                <td>{{ $item->total_amount - $item->paid_amount_sum_paid_amount }}</td>

                                                <td>
                                                    <strong
                                                        class="badge  {{ $item->isFullyProcessed() ? 'bg-success' : 'bg-warning' }}">{{ $item->isFullyProcessed() ? 'Complete' : 'Pending' }}</strong>
                                                    <hr>
                                                    <span
                                                        class="badge bg-{{ $item->status==\App\Models\Invoice::$deliveryStatusArray[0] ? 'danger' : 'success' }}">
                                                    {{ $item->status }}

                                                </span>
                                                </td>
                                                <td>
                                                    @if ( $userGuard->can('invoices.edit'))
                                                        @if (\Carbon\Carbon::parse($item->creation_date)->setTimezone('Asia/Dhaka')->isToday())
                                                            <a href="{{ route($pageHeader['edit_route'],$item->id) }}"
                                                               class="badge bg-info"><i class="fas fa-pen"></i></a>
                                                        @else
                                                            @if(auth()->user()->hasRole('Owner'))
                                                                <a href="{{ route($pageHeader['edit_route'],$item->id) }}"
                                                                   class="badge bg-info"><i class="fas fa-pen"></i></a>
                                                            @endif
                                                        @endif

                                                    @endif
                                                    <a type="button" class="badge bg-primary" data-bs-toggle="modal"
                                                       data-bs-target="#addEmployeeSalaryModal{{$item->id}}"><i
                                                            class="fas fa-dollar"></i>
                                                    </a>
                                                    <a target="_blank"
                                                       href="{{ route('admin.invoices.pdf-preview',$item->id) }}"
                                                       class="badge bg-danger"><i class="fas fa-file-pdf"></i></a>
                                                    <br>
                                                    <a href="{{ route('admin.invoices.show',$item->id) }}"
                                                       class="badge bg-success"><i class="fas fa-eye"></i></a>
                                                    @if ( $userGuard->can('invoices.delete'))
                                                        <a class="badge bg-danger" href="javascript:void(0)"
                                                           onclick="dataDelete({{ $item->id }},'{{ $pageHeader['base_url'] }}')"><i
                                                                class="fas fa-trash"></i></a>
                                                    @endif


                                                    <div class="modal fade" id="addEmployeeSalaryModal{{$item->id}}"
                                                         tabindex="-1"
                                                         aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="modalLabel">Pay
                                                                        Due</h5>
                                                                    <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"
                                                                            aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form method="post"
                                                                          action="{{ route('admin.invoices.due-pay',$item->id) }}">
                                                                        @csrf
                                                                        <div class="mb-3">
                                                                            <label for="month"
                                                                                   class="form-label">Amount</label>
                                                                            <input type="number" step="0.1"
                                                                                   class="form-control"
                                                                                   value="{{ $item->total_amount - $item->paid_amount_sum_paid_amount }}"
                                                                                   id="due_pay" name="due_pay"
                                                                                   required>
                                                                        </div>
                                                                        <button type="submit"
                                                                                class="btn btn-success">Save
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>


                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td>No record Found <a
                                                        href="{{ route($pageHeader['create_route']) }}"
                                                        class="badge btn-info">Create</a></td>
                                            </tr>
                                        @endforelse

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
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>

        @push('scripts')
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
            <script>
                $(document).ready(function () {

                    $('#select2').select2({
                        placeholder: "Select reagents",
                        allowClear: true
                    });
                });
            </script>
            <script>
                document.getElementById('due_filter').addEventListener('change', function () {
                    const dueValue = this.value;
                    const currentUrl = new URL(window.location.href);

                    // Update the URL with the new 'due' query parameter
                    currentUrl.searchParams.set('due', dueValue);

                    // Redirect to the updated URL
                    window.location.href = currentUrl.toString();
                });
            </script>
            <script>
                document.getElementById('dueToggle').addEventListener('change', function () {
                    const value = this.value;
                    document.getElementById('normalTable').style.display = value === 'due' ? 'none' : '';
                    document.getElementById('dueTable').style.display = value === 'due' ? '' : 'none';
                });
            </script>
    @endpush

