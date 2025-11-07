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
                                                <a href="{{ route('admin.invoices.create').'?for='.$item->id }}" class="btn bg-success text-white"><i class="fa fa-flask"
                                                                                                aria-hidden="true"></i>
                                                    <a href="{{ route('admin.admits.create').'?for='.$item->id }}" class="btn bg-dark text-white"><i class="fa fa-bed"
                                                                                                 aria-hidden="true"></i>
                                                    </a>   <a href="{{ route('admin.recepts.create').'?for='.$item->id }}" class="btn bg-info text-white"><i class="fa fa-pager"
                                                                                                 aria-hidden="true"></i>
                                                    </a>
                                                    <a href="{{ route($pageHeader['edit_route'],$item->id) }}"
                                                       class="badge bg-info"><i class="fas fa-pencil"></i></a>
                                                    <a class="badge bg-danger" href="javascript:void(0)"
                                                       onclick="dataDelete({{ $item->id }},'{{ $pageHeader['base_url'] }}')"><i
                                                            class="fas fa-trash"></i></a>
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
                                        <label for="phone">Phone <strong class="text-danger">*</strong></label>
                                        <input id="phone" class="form-control @error('phone') is-invalid @enderror" name="phone" type="text" value="{{ old('phone') }}">
                                        @error('phone')
                                        <strong class="text-danger">{{ $errors->first('phone') }}</strong>
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
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const message = localStorage.getItem('invoiceMessage');
            if (message) {
                $('.success-message').prepend(`<div>${message}</div>`);
                localStorage.removeItem('invoiceMessage'); // Clear the message after displaying it
            }
            function configureAutocomplete(fieldId, sourceUrl, onSelectCallback) {
                let enterEnabled = false;

                $(`#${fieldId}`).autocomplete({
                    source: function (request, response) {
                        if (!request.term.trim()) return response([]);
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
                            }
                        });
                    },
                    minLength: 1,
                    select: function (event, ui) {
                        $(`#${fieldId}`).val(ui.item.phone);
                        enterEnabled = true;
                        if (typeof onSelectCallback === "function") onSelectCallback(ui.item);
                        return false;
                    },
                    close: function() {
                        enterEnabled = true;
                    }
                });

                // Enter key
                $(`#${fieldId}`).on('keydown', function(e) {
                    if (e.key === "Enter" && enterEnabled) {
                        e.preventDefault();
                        const phone = $(this).val().trim();
                        if (phone) {
                            searchByPhone(phone);
                            enterEnabled = false;
                        }
                    }
                });
            }

            function searchByPhone(phone) {
                console.log("Searching for:", phone);
                // Example AJAX
                /*
                $.get('/admin/search-phone-info', { phone }, function(data) {
                    // handle result
                });
                */
            }

            configureAutocomplete("search", "/admin/search-phone", function(item) {
                $("#phone").val(item.phone);
                $("#user_id").val(item.id);
            });

            $(document).on('click', '.opencreateUser', function() {
                let target = $(this).attr('data-target-input');
                $('#createUsermodal').data('target-input', target).modal('show');
            });
            $('#createUser').on('submit', function(e) {
                e.preventDefault();

                let formData = $(this).serialize()
                $.ajax({
                    url: "{{ route('admin.users.store.api') }}",
                    type: "POST",
                    data: formData,
                    success: function(response) {

                        if(response.id){
                            const id = response.id;
                            const customer_name = response.name;
                            localStorage.removeItem('invoiceMessage');
                            localStorage.setItem('invoiceMessage', `<p class="alert alert-success text-center">"${customer_name}" new custer is added: ${id}</p>`);
                            location.reload();
                        }
                        else{
                            location.reload();
                            localStorage.setItem('invoiceMessage', `<p class="alert alert-danger text-center">Something Went wrong try again</p>`);

                        }
                    }
                });
            });

        });



    </script>
@endpush
