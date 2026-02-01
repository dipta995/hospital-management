@extends('backend.layouts.master')
@section('title')
    {{ $pageHeader['title'] }}
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
                            <h4 class="card-title">Patient's List</h4>
                           <div class="row">
                               <div class="col-md-8">
                                   <form action="" class="row" autocomplete="off" method="get">
                                       <div class="col-md-6">
                                           <input type="text" id="search" name="query" placeholder="Enter your phone no" class="search form-control">
                                           <div id="suggestions" class="list-group position-absolute" style="z-index: 1000;"></div>
                                       </div>
                                   </form>
                               </div>
                               <div class="col-md-4">
                                   <button type="button" class="btn btn-info opencreateUser" data-target-input="dr_refer_name">Add Patient</button>
                               </div>
                           </div>

                            <p class="card-description">
                                @include('backend.layouts.partials.message')
                                <span class="success-message"></span>
                            </p>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($datas as $item)
                                        <tr id="table-data{{ $item->id }}">
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->phone }}</td>
                                            <td>{{ $item->address }}</td>
                                            <td>
                                                <a href="{{ route('admin.invoices.create').'?for='.$item->id }}" class="btn bg-success text-white">
                                                    <i class="fa fa-flask" aria-hidden="true"></i>
                                                </a>

                                                @php
                                                    $activeAdmit = $item->admits()->whereNull('release_at')->latest('id')->first();
                                                @endphp

                                                @if($activeAdmit)
                                                    <a href="{{ route('admin.recepts.index').'?for='.$activeAdmit->id }}" class="btn bg-primary text-white" title="Recept List">
                                                        <i class="fa fa-file-invoice" aria-hidden="true"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('admin.admits.create').'?for='.$item->id }}" class="btn bg-dark text-white" title="Admit Patient">
                                                        <i class="fa fa-bed" aria-hidden="true"></i>
                                                    </a>
                                                @endif

                                                <a href="{{ route($pageHeader['edit_route'],$item->id) }}" class="badge bg-info">
                                                    <i class="fas fa-pencil"></i>
                                                </a>
                                                <a class="badge bg-danger" href="javascript:void(0)"
                                                   onclick="dataDelete({{ $item->id }},'{{ $pageHeader['base_url'] }}')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td>No record Found <a href="{{ route($pageHeader['create_route']) }}"
                                                                   class="badge btn-info">Create</a></td>
                                        </tr>
                                    @endforelse

                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-end">
                                    {!! $datas->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- Other Add Modal -->
        <div class="modal fade" id="createUsermodal" tabindex="-1">
            <div class="modal-dialog">
                <form id="createUser">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New Patient</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <fieldset class="row">
                                <div class="col-md-4">
                                    <div class="form-group position-relative"> <!-- important: relative -->
                                        <label for="phone">Phone <strong class="text-danger">*</strong></label>
                                        <input id="phone-new" class="form-control @error('phone') is-invalid @enderror" name="phone" type="text" value="{{ old('phone') }}">
                                        <div id="suggestionAction" class="list-group position-absolute w-100" style="z-index: 1055;"></div>
                                        @error('phone')
                                        <strong class="text-danger">{{ $errors->first('phone') }}</strong>
                                        @enderror
                                    </div>

                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">Name <strong class="text-danger">*</strong></label>
                                        <input id="name" class="form-control @error('name') is-invalid @enderror" name="name" type="text" value="{{ old('name') }}">
                                        @error('name')
                                        <strong class="text-danger">{{ $errors->first('name') }}</strong>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="age">Age <strong class="text-danger">*</strong></label>
                                        <input id="age" class="form-control @error('age') is-invalid @enderror" name="age" type="text" value="{{ old('age') }}">
                                        @error('age')
                                        <strong class="text-danger">{{ $errors->first('age') }}</strong>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="address">Address <strong class="text-danger">*</strong></label>
                                        <input id="address" class="form-control @error('address') is-invalid @enderror" name="address" type="text" value="{{ old('address') }}">
                                        @error('address')
                                        <strong class="text-danger">{{ $errors->first('address') }}</strong>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-default.label required="true" for="blood_group">Blood Group</x-default.label>
                                        <select class="form-control" name="blood_group" id="blood_group">
                                            <option value="">--Choose--</option>
                                            <option value="A+">A+</option>
                                            <option value="A-">A-</option>
                                            <option value="B+">B+</option>
                                            <option value="B-">B-</option>
                                            <option value="AB+">AB+</option>
                                            <option value="AB-">AB-</option>
                                            <option value="O+">O+</option>
                                            <option value="O-">O-</option>
                                        </select>
                                        <x-default.input-error name="blood_group"></x-default.input-error>
                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-default.label required="true" for="gender">Gender</x-default.label>
                                        <select class="form-control" name="gender" id="gender">
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">Other</option>
                                        </select>
                                        <x-default.input-error name="gender"></x-default.input-error>
                                    </div>
                                </div>
                                  <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="marital_status">Marital Status</label>
                                        <select class="form-control" name="marital_status" id="marital_status">
                                            <option value="">-- Select --</option>
                                            <option value="Single">Single</option>
                                            <option value="Married">Married</option>
                                            <option value="Divorced">Divorced</option>
                                            <option value="Widowed">Widowed</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="occupation">Occupation</label>
                                        <input id="occupation" class="form-control" name="occupation" type="text" value="{{ old('occupation') }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="religion">Religion</label>
                                        <input id="religion" class="form-control" name="religion" type="text" value="{{ old('religion') }}">
                                    </div>
                                </div>



                                {{--                                    <div class="col-md-4">--}}
                                {{--                                        <div class="form-group">--}}
                                {{--                                            <label for="password">Password <strong class="text-danger">*</strong></label>--}}
                                {{--                                            <input class="form-control @error('password') is-invalid @enderror" name="password" type="password">--}}
                                {{--                                            @error('password')--}}
                                {{--                                            <strong class="text-danger">{{ $errors->first('password') }}</strong>--}}
                                {{--                                            @enderror--}}
                                {{--                                        </div>--}}
                                {{--                                    </div>--}}
                                {{--                                    <div class="col-md-4">--}}
                                {{--                                        <div class="form-group">--}}
                                {{--                                            <label for="password_confirmation">Confirm password <strong class="text-danger">*</strong></label>--}}
                                {{--                                            <input class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" type="password">--}}
                                {{--                                            @error('password_confirmation')--}}
                                {{--                                            <strong class="text-danger">{{ $errors->first('password_confirmation') }}</strong>--}}
                                {{--                                            @enderror--}}
                                {{--                                        </div>--}}
                                {{--                                    </div>--}}

                            </fieldset>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
    <!-- main-panel ends -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Function to configure autocomplete
            function configureAutocomplete(fieldId, sourceUrl, onSelectCallback) {
                const $field = $("#" + fieldId);

                $field.autocomplete({
                    minLength: 4, // Start autocomplete after 4 digits
                    source: function (request, response) {
                        if (request.term.trim().length < 4) return response([]);
                        $.ajax({
                            url: sourceUrl,
                            type: "GET",
                            dataType: "json",
                            data: { query: request.term },
                            success: function (data) {
                                response(data.map(item => ({
                                    label: `${item.name} (${item.phone})`,
                                    value: item.phone,
                                    ...item
                                })));
                            },
                            error: function () {
                                console.log("Error fetching data");
                                response([]);
                            }
                        });
                    },
                    select: function (event, ui) {
                        // Set the phone field value
                        $field.val(ui.item.phone);

                        // Run callback to set other fields (like name)
                        if (typeof onSelectCallback === "function") {
                            onSelectCallback(ui.item);
                        }

                        return false; // Prevent default value replacement
                    }
                });

                // Remove "Go To Task" button when typing again
                $field.on("input", function () {
                    $("#gotoTaskBtn").remove();
                });
            }

            // Configure autocomplete for modal phone field
            $('#phone-new').autocomplete({
                appendTo: "#suggestionAction", // attach suggestion list to your div
                minLength: 4,
                source: function(request, response) {
                    if(request.term.length < 4) return response([]);
                    $.ajax({
                        url: "/admin/search-phone",
                        type: "GET",
                        data: { query: request.term },
                        success: function(data) {
                            response(data.map(item => ({
                                label: `${item.name} (${item.phone})`,
                                value: item.phone,
                                userId: item.userId,
                                name: item.name,
                            })));
                        }
                    });
                },
                select: function(event, ui) {
                    $('#phone-new').val(ui.item.phone);
                    $('#name').val(ui.item.name);

                    const userId = ui.item.userId;

                    // Remove old buttons if exist
                    $('#gotoTaskBtn, #invoiceBtn, #admitBtn').remove();

                    // Append buttons below input
                    $('#phone-new').closest('.form-group').append(`
            <div class="mt-2" id="actionButtons">
                <a href="/admin/invoices/create?for=${userId}" class="btn bg-success text-white">
                    <i class="fa fa-flask" aria-hidden="true"></i>
                </a>
                <a href="/admin/admits/create?for=${userId}" class="btn bg-dark text-white">
                    <i class="fa fa-bed" aria-hidden="true"></i>
                </a>
            </div>
        `);

                    return false;
                }
            });

            // Configure autocomplete for main search (if needed)
            configureAutocomplete("search", "/admin/search-phone", function(item) {
                $("#phone").val(item.phone);
                $("#user_id").val(item.id);
            });

            // Open modal
            $(document).on('click', '.opencreateUser', function() {
                let target = $(this).attr('data-target-input');
                $('#createUsermodal').data('target-input', target).modal('show');
            });

            // Submit modal form via AJAX
            $('#createUser').on('submit', function(e) {
                e.preventDefault();

                let formData = $(this).serialize();
                $.ajax({
                    url: "{{ route('admin.users.store.api') }}",
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        if(response.id){
                            const id = response.id;
                            const customer_name = response.name;
                            localStorage.removeItem('invoiceMessage');
                            localStorage.setItem('invoiceMessage', `<p class="alert alert-success text-center">"${customer_name}" new customer added: ${id}</p>`);
                            location.reload();
                        } else {
                            localStorage.setItem('invoiceMessage', `<p class="alert alert-danger text-center">Something went wrong. Try again!</p>`);
                            location.reload();
                        }
                    },
                    error: function() {
                        localStorage.setItem('invoiceMessage', `<p class="alert alert-danger text-center">Something went wrong. Try again!</p>`);
                        location.reload();
                    }
                });
            });
        });

    </script>

@endpush
