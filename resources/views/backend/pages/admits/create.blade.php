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

                {{-- Admit Form --}}
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Admit Patient</h4>
                        @include('backend.layouts.partials.message')
<fieldset>
                        <form method="POST" action="{{ route($pageHeader['store_route']) }}">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">

                            <div class="form-group mb-3">
                                <label for="admit_at" class="form-label">Admit Date</label>
                                <input type="datetime-local" class="form-control" name="admit_at" id="admit_at" required>
                            </div>
                            <div class="form-group mb-3">
                                    <x-default.label required="true" for="dr_refer_name">Dr Name</x-default.label>
                                    <x-default.input name="dr_refer_name" class="form-control" required id="dr_refer_name" type="text" ></x-default.input>
                                    <x-default.input-error name="dr_refer_name"></x-default.input-error>
                                    <input type="hidden" name="dr_refer_id" id="dr_refer_id">
                            </div>

                            {{-- <div class="form-group mb-3">
                                <label for="release_at" class="form-label">Release Date</label>
                                <input type="datetime-local" class="form-control" name="release_at" id="release_at">
                            </div> --}}

                            <div class="form-group mb-3">
                                <label for="nid" class="form-label">NID</label>
                                <input type="text" class="form-control" name="nid" id="nid" placeholder="Enter NID">
                            </div>

                            <div class="form-group mb-3">
                                <label for="note" class="form-label">Note</label>
                                <textarea name="note" id="note" class="form-control" rows="3"></textarea>
                            </div>

                            <button type="submit" class="btn btn-success float-end mt-2">
                                Admit Now
                            </button>
                        </form>
</fieldset>
                    </div>
                </div>

            </div>
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
                        data: { query: request.term },
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
        });
    </script>
@endpush
