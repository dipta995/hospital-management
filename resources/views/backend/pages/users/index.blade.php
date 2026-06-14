@extends('backend.layouts.master')
@section('title')
    {{ $pageHeader['title'] }}
@endsection

@push('styles')
    @include('backend.layouts.partials.crud-styles')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@section('admin-content')
    <div class="crud-page container-fluid py-3">
        @php
            $heroActions = '<button type="button" class="btn-crud-primary opencreateUser" data-target-input="dr_refer_name"><i class="fas fa-plus"></i> Quick Add Patient</button>';
        @endphp
        @include('backend.layouts.partials.crud-hero', [
            'heroTitle' => 'Patients',
            'heroSubtitle' => 'Search patients, manage records and quick actions',
            'heroIcon' => 'fa-user-injured',
            'heroActions' => $heroActions,
        ])

        <div class="crud-card">
            @include('backend.layouts.partials.message')
            <span class="success-message"></span>

            <form action="" class="crud-toolbar" autocomplete="off" method="get">
                <div class="row g-2 align-items-end flex-grow-1">
                    <div class="col-md-5 position-relative">
                        <label for="search" class="form-label">Search by Phone</label>
                        <input type="text" id="search" name="query" placeholder="Enter phone number" class="form-control search" value="{{ request('query') }}">
                        <div id="suggestions" class="list-group position-absolute w-100" style="z-index: 1000;"></div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </div>
            </form>

            <div class="crud-table-wrap">
                <div class="table-responsive">
                    <table class="table crud-table table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Balance</th>
                            <th class="text-end">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($datas as $item)
                            <tr id="table-data{{ $item->id }}">
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $item->name }}</strong></td>
                                <td>{{ $item->phone }}</td>
                                <td>{{ $item->address ?: '—' }}</td>
                                <td>
                                    @php $customerBalance = $item->customerBalance; @endphp
                                    <span class="crud-badge">
                                        {{ $customerBalance ? number_format($customerBalance->balance, 2) : '0.00' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="crud-action-group">
                                        <a href="{{ route('admin.patients.profile', ['user_id' => $item->id, 'phone' => $item->phone]) }}" class="crud-btn-icon crud-btn-info" title="Patient 360">
                                            <i class="fas fa-id-card"></i>
                                        </a>
                                        <a href="{{ route('admin.invoices.create').'?for='.$item->id }}" class="crud-btn-icon crud-btn-view" title="Create Invoice">
                                            <i class="fa fa-flask"></i>
                                        </a>

                                        @php $activeAdmit = $item->admits()->whereNull('release_at')->latest('id')->first(); @endphp

                                        @if($activeAdmit)
                                            <a href="{{ route('admin.recepts.index').'?for='.$activeAdmit->id }}" class="crud-btn-icon crud-btn-info" title="Recept List">
                                                <i class="fa fa-file-invoice"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('admin.admits.create').'?for='.$item->id }}" class="crud-btn-icon crud-btn-dark" title="Admit Patient">
                                                <i class="fa fa-bed"></i>
                                            </a>
                                        @endif

                                        <a href="{{ route('admin.admits.index').'?user_id='.$item->id }}" class="crud-btn-icon crud-btn-info" title="Admit History">
                                            <i class="fas fa-list"></i>
                                        </a>

                                        @if($customerBalance)
                                            <a href="{{ route('admin.customer_balances.edit', $customerBalance->id) }}" class="crud-btn-icon crud-btn-warning" title="Edit Balance">
                                                <i class="fas fa-wallet"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('admin.customer_balances.create').'?user_id='.$item->id }}" class="crud-btn-icon crud-btn-warning" title="Add Balance">
                                                <i class="fas fa-wallet"></i>
                                            </a>
                                        @endif

                                        <a href="{{ route($pageHeader['edit_route'], $item->id) }}" class="crud-btn-icon crud-btn-edit" title="Edit">
                                            <i class="fas fa-pencil"></i>
                                        </a>
                                        <a href="javascript:void(0)" class="crud-btn-icon crud-btn-delete" title="Delete"
                                           onclick="dataDelete({{ $item->id }},'{{ $pageHeader['base_url'] }}')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="crud-empty">
                                    No patients found.
                                    <button type="button" class="btn btn-sm btn-primary ms-2 opencreateUser" data-target-input="dr_refer_name">Add Patient</button>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {!! $datas->links() !!}
            </div>
        </div>
    </div>

    <div class="modal fade crud-modal" id="createUsermodal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form id="createUser">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Patient</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row crud-form-grid g-3">
                            <div class="col-md-4">
                                <div class="form-group position-relative">
                                    <label for="phone-new">Phone <span class="text-danger">*</span></label>
                                    <input id="phone-new" class="form-control @error('phone') is-invalid @enderror" name="phone" type="text" value="{{ old('phone') }}">
                                    <div id="suggestionAction" class="list-group position-absolute w-100" style="z-index: 1055;"></div>
                                    @error('phone')
                                        <div class="text-danger small mt-1">{{ $errors->first('phone') }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input id="name" class="form-control @error('name') is-invalid @enderror" name="name" type="text" value="{{ old('name') }}">
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $errors->first('name') }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="age">Age <span class="text-danger">*</span></label>
                                <input id="age" class="form-control @error('age') is-invalid @enderror" name="age" type="text" value="{{ old('age') }}">
                                @error('age')
                                    <div class="text-danger small mt-1">{{ $errors->first('age') }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="address">Address <span class="text-danger">*</span></label>
                                <input id="address" class="form-control @error('address') is-invalid @enderror" name="address" type="text" value="{{ old('address') }}">
                                @error('address')
                                    <div class="text-danger small mt-1">{{ $errors->first('address') }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <x-default.label required="true" for="blood_group">Blood Group</x-default.label>
                                <select class="form-select" name="blood_group" id="blood_group">
                                    <option value="">-- Select --</option>
                                    @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $group)
                                        <option value="{{ $group }}">{{ $group }}</option>
                                    @endforeach
                                </select>
                                <x-default.input-error name="blood_group"></x-default.input-error>
                            </div>
                            <div class="col-md-4">
                                <x-default.label required="true" for="gender">Gender</x-default.label>
                                <select class="form-select" name="gender" id="gender">
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                                <x-default.input-error name="gender"></x-default.input-error>
                            </div>
                            <div class="col-md-4">
                                <label for="marital_status">Marital Status</label>
                                <select class="form-select" name="marital_status" id="marital_status">
                                    <option value="">-- Select --</option>
                                    @foreach(['Single', 'Married', 'Divorced', 'Widowed'] as $status)
                                        <option value="{{ $status }}">{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="occupation">Occupation</label>
                                <input id="occupation" class="form-control" name="occupation" type="text" value="{{ old('occupation') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="religion">Religion</label>
                                <input id="religion" class="form-control" name="religion" type="text" value="{{ old('religion') }}">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-crud-submit">Save Patient</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
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

            function configureAutocomplete(fieldId, sourceUrl, onSelectCallback) {
                const $field = $("#" + fieldId);

                $field.autocomplete({
                    minLength: 4,
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
                                response([]);
                            }
                        });
                    },
                    select: function (event, ui) {
                        $field.val(ui.item.phone);
                        if (typeof onSelectCallback === "function") {
                            onSelectCallback(ui.item);
                        }
                        return false;
                    }
                });

                $field.on("input", function () {
                    $("#gotoTaskBtn").remove();
                });
            }

            $('#phone-new').autocomplete({
                appendTo: "#suggestionAction",
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
                    $('#gotoTaskBtn, #invoiceBtn, #admitBtn').remove();

                    $('#phone-new').closest('.form-group').append(`
                        <div class="mt-2" id="actionButtons">
                            <a href="/admin/invoices/create?for=${userId}" class="crud-btn-icon crud-btn-view" title="Invoice">
                                <i class="fa fa-flask"></i>
                            </a>
                            <a href="/admin/admits/create?for=${userId}" class="crud-btn-icon crud-btn-dark" title="Admit">
                                <i class="fa fa-bed"></i>
                            </a>
                        </div>
                    `);

                    return false;
                }
            });

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

                $.ajax({
                    url: "{{ route('admin.users.store.api') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        if(response.id){
                            localStorage.removeItem('invoiceMessage');
                            localStorage.setItem('invoiceMessage', `<p class="alert alert-success text-center">"${response.name}" new customer added: ${response.id}</p>`);
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
