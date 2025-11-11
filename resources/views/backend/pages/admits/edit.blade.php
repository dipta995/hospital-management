@extends('backend.layouts.master')

@section('title')
    Edit {{ $pageHeader['title'] }}
@endsection

@section('admin-content')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Edit Admit Information</h4>

                        @include('backend.layouts.partials.message')

                        <form method="POST" action="{{ route($pageHeader['update_route'], $edited->id) }}">
                            @csrf
                            @method('PUT')

                           <div class="form-group mb-3">
                                <label>Patient Name</label>
                                <input type="text" class="form-control" value="{{ optional($edited->user)->name ?? 'Unknown' }}" disabled>
                            </div>
                            <div class="form-group mb-3">
                                <x-default.label  for="dr_refer_name">Dr Name                                                     <button type="button" class="badge bg-info openDoctorModal" data-target-input="dr_refer_name">Add Doctor</button>
                                </x-default.label>
                                <x-default.input name="dr_refer_name" class="form-control" value="{{ $edited->reefer->name ?? '' }}"
                                                 id="dr_refer_name" type="text"></x-default.input>
                                <x-default.input-error name="dr_refer_name"></x-default.input-error>
                                <input type="hidden" name="dr_refer_id" value="{{ $edited->reffer_id ?? '' }}" id="dr_refer_id">
                            </div>


                            <div class="form-group mb-3">
                                <label for="bed_cabin">Bed/Cabin</label>
                                <input type="text" id="bed_cabin" name="bed_cabin" class="form-control"
                                    value="{{ old('bed_cabin', $edited->bed_cabin) }}" placeholder="Enter bed or cabin number">
                            </div>

                            <div class="form-group mb-3">
                                <label for="father_spouse">Father/Spouse</label>
                                <input type="text" id="father_spouse" name="father_spouse" class="form-control"
                                    value="{{ old('father_spouse', $edited->father_spouse) }}" placeholder="Enter father or spouse name">
                            </div>

                            <div class="form-group mb-3">
                                <label for="received_by">Received By</label>
                                <input type="text" id="received_by" name="received_by" class="form-control"
                                    value="{{ old('received_by', $edited->received_by) }}" placeholder="Enter who received the patient">
                            </div>

                            <div class="form-group mb-3">
                                <label for="clinical_diagnosis">Clinical Diagnosis</label>
                                <textarea id="clinical_diagnosis" name="clinical_diagnosis" class="form-control" rows="3"
                                        placeholder="Enter clinical diagnosis">{{ old('clinical_diagnosis', $edited->clinical_diagnosis) }}</textarea>
                            </div>

                            <div class="form-group mb-3">
                                <label for="refer_id">Refer ID</label>
                                <input type="text" id="refer_id" name="refer_id" class="form-control"
                                    value="{{ old('refer_id', $edited->refer_id) }}" placeholder="Enter refer ID">
                            </div>

                            {{-- <div class="form-group mb-3">
                                <label for="dr_refer_id">Dr Refer ID</label>
                                <input type="text" id="dr_refer_id" name="dr_refer_id" class="form-control"
                                    value="{{ old('dr_refer_id', $edited->dr_refer_id) }}" placeholder="Enter doctor refer ID">
                            </div> --}}



                            <div class="form-group mb-3">
                                <label for="admit_at">Admit Date <strong class="text-danger">*</strong></label>
                                <input type="date" id="admit_at" name="admit_at" class="form-control"
                                       value="{{ old('admit_at', $edited->admit_at ? date('Y-m-d', strtotime($edited->admit_at)) : '') }}" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="release_at">Release Date</label>
                                <input type="date" id="release_at" name="release_at" class="form-control"
                                       value="{{ old('release_at', $edited->release_at ? date('Y-m-d', strtotime($edited->release_at)) : '') }}">
                            </div>

                            <div class="form-group mb-3">
                                <label for="nid">NID Number</label>
                                <input type="text" id="nid" name="nid" class="form-control"
                                       value="{{ old('nid', $edited->nid) }}">
                            </div>


                            <div class="form-group mb-3">
                                <label for="note">Note</label>
                                <textarea id="note" name="note" class="form-control" rows="4">{{ old('note', $edited->note) }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-success float-end">
                                <i class="fas fa-save"></i> Update Admit
                            </button>
                        </form>

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
                                <input type="text" class="form-control" name="phone" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Parcent</label>
                                <input type="number" class="form-control" name="percent" value="0" required>
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
        });
    </script>
@endpush
