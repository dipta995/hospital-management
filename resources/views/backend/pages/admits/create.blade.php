@extends('backend.layouts.master')

@section('title')
    Create New {{ $pageHeader['title'] }}
@endsection
@push('style')

@endpush

@section('admin-content')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12">


                    {{-- Admit Form --}}
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Admit Patient</h4>
                            @include('backend.layouts.partials.message')
                            <fieldset>
                                <form method="POST" action="{{ route($pageHeader['store_route']) }}">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <label for="admit_at" class="form-label">Admit Date</label>
                                                <input type="datetime-local" class="form-control" name="admit_at" id="admit_at" required>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <x-default.label for="dr_refer_name">
                                                    Dr Name
                                                    <button type="button" class="badge bg-info openDoctorModal" data-target-input="dr_refer_name">Add Doctor</button>
                                                </x-default.label>
                                                <x-default.input name="dr_refer_name" class="form-control" id="dr_refer_name" type="text"></x-default.input>
                                                <x-default.input-error name="dr_refer_name"></x-default.input-error>
                                                <input type="hidden" name="dr_refer_id" id="dr_refer_id">
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <label for="bed_or_cabin" class="form-label">Bed/Cabin</label>
                                                <input type="text" class="form-control" name="bed_or_cabin" id="bed_or_cabin" placeholder="Enter bed or cabin number">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <label for="father_or_spouse" class="form-label">Father/Spouse</label>
                                                <input type="text" class="form-control" name="father_or_spouse" id="father_or_spouse" placeholder="Enter father's or spouse's name">
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <label for="received_by" class="form-label">Received By</label>
                                                <input type="text" class="form-control" name="received_by" id="received_by" placeholder="Received by (staff name)">
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <label for="nid" class="form-label">NID</label>
                                                <input type="text" class="form-control" name="nid" id="nid" placeholder="Enter NID">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <label for="refer_id" class="form-label">Refer ID(PC)
                                                    <button type="button" class="badge bg-info openOtherModal" data-target-input="refer_name">Add PC</button>
                                                </label>
                                                <x-default.input name="refer_name" required class="form-control" id="refer_name" type="text"></x-default.input>
                                                <x-default.input-error name="refer_name"></x-default.input-error>
                                                <input type="hidden" name="refer_id" id="refer_id">
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <label for="clinical_diagnosis" class="form-label">Clinical Diagnosis</label>
                                                <textarea name="clinical_diagnosis" id="clinical_diagnosis" class="form-control" rows="2" placeholder="Enter diagnosis details"></textarea>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <label for="note" class="form-label">Note</label>
                                                <textarea name="note" id="note" class="form-control" rows="2" placeholder="Additional note"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-success float-end mt-2">
                                        Admit Now
                                    </button>
                                </form>
                            </fieldset>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h4 class="card-title mb-3">Patient Information</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>ID</th>
                                        <td>{{ $user->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Name</th>
                                        <td>{{ $user->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Phone</th>
                                        <td>{{ $user->phone ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Age</th>
                                        <td>{{ $user->age ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Gender</th>
                                        <td>{{ $user->gender ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Blood Group</th>
                                        <td>{{ $user->blood_group ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td>{{ $user->address ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- Doctor Add Modal -->
        <div class="modal fade" id="doctorAddModal" tabindex="-1">
            <div class="modal-dialog">
                <form id="doctorAddForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New Doctor</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Doctor Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" name="phone" >
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Parcent</label>
                                <input type="number" class="form-control" name="percent" value="0" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Designation</label>
                                <input type="text" class="form-control" name="designation" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Type</label>
                                <select class="form-control" name="type" required>
                                    <option value="{{ \App\Models\Reefer::$typeArray[0] }}">{{ \App\Models\Reefer::$typeArray[0] }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Save Doctor</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="otherAddModal" tabindex="-1">
            <div class="modal-dialog">
                <form id="otherAddForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New PC</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">PC Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" name="phone" >
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Parent(%)</label>
                                <input type="number" class="form-control" name="percent" value="0" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Type</label>
                                <select class="form-control" name="type" required>
                                    <option value="{{ \App\Models\Reefer::$typeArray[1] }}">{{ \App\Models\Reefer::$typeArray[1] }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Save PC</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script>
        $(document).ready(function () {
            function configureAutocomplete(fieldId, sourceUrl, onSelectCallback) {
                $(`#${fieldId}`).autocomplete({
                    source: function (request, response) {
                        if (request.term.trim() === "") {
                            response([]);
                            return;
                        }
                        $.ajax({
                            url: sourceUrl,
                            type: "GET",
                            data: {query: request.term},
                            success: function (data) {
                                response(data.map(item => ({
                                    label: item.name,
                                    value: item.id,
                                    ...item
                                })));
                            },
                            error: function () {
                                alert("Error fetching data.");
                            }
                        });
                    },
                    select: function (event, ui) {
                        onSelectCallback(ui.item);
                    },
                    minLength: 1
                });
            }

            // Configure doctors autocomplete
            configureAutocomplete("dr_refer_name", "/admin/get-doctors", function (item) {
                $("#dr_refer_id").val(item.referID);
                $("#dr_refer_name").val(item.name);
            });
            // Configure referrals autocomplete
            configureAutocomplete("refer_name", "/admin/get-referrals", function (item) {
                $("#refer_id").val(item.referID);
                $("#refer_name").val(item.name);
            });


            $(document).on('click', '.openDoctorModal', function() {
                let target = $(this).attr('data-target-input');
                $('#doctorAddModal').data('target-input', target).modal('show');
            }); $(document).on('click', '.openOtherModal', function() {
                let target = $(this).attr('data-target-input');
                $('#otherAddModal').data('target-input', target).modal('show');
            });

            $('#doctorAddForm').on('submit', function(e) {
                e.preventDefault();

                let formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('admin.reefers.store') }}",
                    type: "POST",
                    data: formData,
                    success: function(response) {

                        let targetInput = $('#doctorAddModal').data('target-input');

                        $("#" + targetInput).val(response.name);  // insert name
                        $("#"+targetInput.replace("_name","_id")).val(response.id); // insert id

                        $('#doctorAddModal').modal('hide');
                        $('#doctorAddForm')[0].reset();
                    }
                });
            });
            $('#otherAddForm').on('submit', function(e) {
                e.preventDefault();

                let formData = $(this).serialize()
                $.ajax({
                    url: "{{ route('admin.reefers.store') }}",
                    type: "POST",
                    data: formData,
                    success: function(response) {

                        let targetInput = $('#otherAddModal').data('target-input');

                        $("#" + targetInput).val(response.name);  // insert name
                        $("#"+targetInput.replace("_name","_id")).val(response.id); // insert id

                        $('#otherAddModal').modal('hide');
                        $('#otherAddForm')[0].reset();
                    }
                });
            });
        });
    </script>
@endpush
