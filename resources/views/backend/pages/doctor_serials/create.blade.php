@extends('backend.layouts.master')

@section('title')
    Create {{ $pageHeader['title'] }}
@endsection

@push('styles')
    @include('backend.layouts.partials.crud-styles')
    @include('backend.layouts.partials.cost-category-select2-assets')
@endpush

@section('admin-content')
    <div class="crud-page container-fluid py-3">
        @include('backend.layouts.partials.crud-form-hero', [
            'formTitle' => 'Create Doctor Serial',
            'formSubtitle' => 'Book a patient serial — auto-generates next number & ETA',
            'formIcon' => 'fa-ticket-alt',
        ])

        <div class="crud-card">
            @include('backend.layouts.partials.message')

            <form method="post" action="{{ route($pageHeader['store_route']) }}">
                @csrf

                <div class="crud-form-section">
                    <div class="crud-form-section-header"><i class="fas fa-user-md"></i> Doctor & Appointment</div>
                    <div class="crud-form-section-body">
                        <div class="row crud-form-grid g-3">
                            <div class="col-md-6">
                                <x-default.label required="true" for="reefer_id">Doctor</x-default.label>
                                <select class="form-select cost-category-select" name="reefer_id" id="reefer_id" required data-placeholder="Search doctor...">
                                    <option value=""></option>
                                    @foreach($reefers as $item)
                                        <option value="{{ $item->id }}" @selected(old('reefer_id') == $item->id)>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                <x-default.input-error name="reefer_id" />
                            </div>
                            <div class="col-md-6">
                                <x-default.label required="true" for="date">Appointment Date</x-default.label>
                                <input id="date" class="form-control" name="date" type="date"
                                       value="{{ old('date', \Carbon\Carbon::now('Asia/Dhaka')->format('Y-m-d')) }}"
                                       min="{{ \Carbon\Carbon::now('Asia/Dhaka')->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-6">
                                <x-default.label required="true" for="serial_number">Serial Number</x-default.label>
                                <div class="input-group">
                                    <input name="serial_number" class="form-control" id="serial_number" type="text"
                                           value="{{ old('serial_number') }}" placeholder="Auto" readonly required>
                                    <button type="button" id="refresh_serial_btn" class="btn btn-outline-primary">Refresh</button>
                                </div>
                                <div class="d-flex align-items-center gap-3 mt-2">
                                    <label class="mb-0 d-flex align-items-center gap-2">
                                        <input type="checkbox" id="serial_manual_toggle"> Set manually
                                    </label>
                                    <small id="serial_help" class="text-muted">Select a doctor to auto-generate serial</small>
                                </div>
                                <x-default.input-error name="serial_number" />
                            </div>
                            <div class="col-md-6">
                                <x-default.label for="amount">Consultation Fee (৳)</x-default.label>
                                <input name="amount" class="form-control" id="amount" type="number" step="0.01" min="0"
                                       value="{{ old('amount') }}" placeholder="Optional">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="crud-form-section">
                    <div class="crud-form-section-header"><i class="fas fa-user"></i> Patient Information</div>
                    <div class="crud-form-section-body">
                        <div class="row crud-form-grid g-3">
                            <div class="col-md-6">
                                <x-default.label required="true" for="patient_name">Full Name</x-default.label>
                                <input name="patient_name" class="form-control" id="patient_name" type="text"
                                       value="{{ old('patient_name') }}" required placeholder="Patient full name">
                                <x-default.input-error name="patient_name" />
                            </div>
                            <div class="col-md-3">
                                <x-default.label for="patient_phone">Phone</x-default.label>
                                <input name="patient_phone" class="form-control" id="patient_phone" type="text"
                                       value="{{ old('patient_phone') }}" placeholder="01XXXXXXXXX">
                                <x-default.input-error name="patient_phone" />
                            </div>
                            <div class="col-md-3">
                                <x-default.label for="patient_age_year">Age</x-default.label>
                                <input name="patient_age_year" class="form-control" id="patient_age_year" type="text"
                                       value="{{ old('patient_age_year') }}">
                            </div>
                            <div class="col-md-4">
                                <x-default.label for="patient_gender">Gender</x-default.label>
                                <select name="patient_gender" id="patient_gender" class="form-select">
                                    <option value="">Select</option>
                                    @foreach(['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $val => $label)
                                        <option value="{{ $val }}" @selected(old('patient_gender') === $val)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <x-default.label for="patient_blood_group">Blood Group</x-default.label>
                                <select name="patient_blood_group" id="patient_blood_group" class="form-select">
                                    <option value="">Select</option>
                                    @foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg)
                                        <option value="{{ $bg }}" @selected(old('patient_blood_group') === $bg)>{{ $bg }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <x-default.label for="patient_email">Email</x-default.label>
                                <input name="patient_email" class="form-control" id="patient_email" type="email" value="{{ old('patient_email') }}">
                            </div>
                            <div class="col-12">
                                <x-default.label for="patient_address">Address</x-default.label>
                                <textarea name="patient_address" id="patient_address" class="form-control" rows="2">{{ old('patient_address') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="crud-form-section">
                    <div class="crud-form-section-header"><i class="fas fa-sticky-note"></i> Additional</div>
                    <div class="crud-form-section-body">
                        <div class="row crud-form-grid g-3">
                            <div class="col-md-8">
                                <x-default.label for="remarks">Remarks</x-default.label>
                                <textarea name="remarks" id="remarks" class="form-control" rows="2">{{ old('remarks') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <x-default.label for="send_sms">SMS Notification</x-default.label>
                                <div class="mt-2">
                                    <label class="me-3"><input name="send_sms" type="radio" value="yes" @checked(old('send_sms', 'yes') === 'yes')> Send SMS</label>
                                    <label><input name="send_sms" type="radio" value="no" @checked(old('send_sms') === 'no')> Don't send</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="crud-form-actions">
                    <a href="{{ route($pageHeader['index_route']) }}" class="btn-crud-cancel">Cancel</a>
                    <button type="submit" class="btn btn-crud-submit">Create Serial</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        (function () {
            var nextUrlTemplate = '{{ route("admin.doctor_serials.next", ["reefer" => "__REEFER__"]) }}';

            function fetchAndFill(reeferId) {
                if (!reeferId) {
                    $('#serial_number').val('');
                    $('#serial_help').text('Select a doctor to auto-generate serial.');
                    return;
                }
                var date = $('#date').val();
                var url = nextUrlTemplate.replace('__REEFER__', reeferId) + (date ? ('?date=' + encodeURIComponent(date)) : '');
                $.get(url).done(function (resp) {
                    if (resp && resp.serial && !$('#serial_manual_toggle').is(':checked')) {
                        $('#serial_number').val(resp.serial);
                    }
                    var help = 'Next serial: #' + (resp.serial || '—');
                    if (resp.approx_time) help += ' · Approx: ' + resp.approx_time;
                    $('#serial_help').text(help);
                }).fail(function () {
                    $('#serial_help').text('Could not fetch serial. Try again.');
                });
            }

            document.addEventListener('DOMContentLoaded', function () {
                $('#reefer_id').select2({ placeholder: 'Search doctor...', allowClear: true, width: '100%', minimumResultsForSearch: 0 });
                $('#reefer_id').on('change', function () { fetchAndFill($(this).val()); });
                $('#date').on('change', function () { fetchAndFill($('#reefer_id').val()); });
                $('#refresh_serial_btn').on('click', function () { fetchAndFill($('#reefer_id').val()); });
                $('#serial_manual_toggle').on('change', function () {
                    var manual = $(this).is(':checked');
                    $('#serial_number').prop('readonly', !manual);
                    if (manual) {
                        $('#serial_help').text('Manual mode — enter serial number yourself.');
                        $('#serial_number').focus();
                    } else {
                        fetchAndFill($('#reefer_id').val());
                    }
                });
                if ($('#reefer_id').val()) fetchAndFill($('#reefer_id').val());
            });
        })();
    </script>
@endpush
