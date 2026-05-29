@extends('backend.layouts.master')

@section('title')
    Create New {{ $pageHeader['title'] }}
@endsection

@push('styles')
    <!-- Add Select2 CSS for better dropdown UI -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <style>
        .section-box {
            border-radius: 6px;
            background: #fff;
            padding: 18px;
            border-left: 4px solid #0d6efd;
            box-shadow: 0 1px 0 rgba(13,110,253,0.06);
            margin-bottom: 18px;
        }
        .section-header { display:flex; align-items:center; gap:12px; margin-bottom:12px; }
        .section-header h5 { margin:0; font-size:16px; font-weight:700; }
        .required-badge { background:#e9f2ff; color:#0d6efd; padding:2px 6px; border-radius:4px; font-size:12px; margin-left:8px; }
        .form-hint { font-size:13px; color:#6c757d; }
    </style>
@endpush


@section('admin-content')

    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Create New {{ $pageHeader['title'] }}</h4>
                            @include('backend.layouts.partials.message')
                            <form class="cmxform" method="post" action="{{ route($pageHeader['store_route']) }}">
                                @csrf

                                <div class="section-box">
                                    <div class="section-header">
                                        <h5><i class="fas fa-user-md"></i> Doctor & Appointment Details</h5>
                                        <span class="required-badge">Required</span>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <x-default.label required="true" for="reefer_id">Doctor Name</x-default.label>
                                                <select class="form-control" name="reefer_id" id="reefer_id">
                                                    <option value="">Search and select a doctor</option>
                                                    @foreach($reefers as $item)
                                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                                <x-default.input-error name="reefer_id" />
                                            </div>

                                            <div class="mb-3">
                                                <x-default.label required="true" for="date">Appointment Date</x-default.label>
                                                <input id="date" class="form-control" name="date" type="date" value="{{ old('date', \Carbon\Carbon::now()->format('Y-m-d')) }}" min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <x-default.label for="serial_number">Serial Number <span class="required-badge">Required</span></x-default.label>
                                                <div style="display:flex; gap:8px; align-items:center;">
                                                    <x-default.input name="serial_number" class="form-control" id="serial_number" type="text" placeholder="e.g., 1, 2, 3..." readonly />
                                                    <button type="button" id="refresh_serial_btn" class="btn btn-outline-primary btn-sm" style="height:40px">Refresh</button>
                                                </div>
                                                <div style="margin-top:6px; display:flex; gap:12px; align-items:center;">
                                                    <label style="display:flex; gap:6px; align-items:center; font-weight:600;">
                                                        <input type="checkbox" id="serial_manual_toggle" /> <span style="font-weight:400;">Set manually</span>
                                                    </label>
                                                    <small id="serial_help" class="form-hint">Enter the appointment serial number</small>
                                                </div>
                                                <x-default.input-error name="serial_number" />
                                            </div>

                                            <div class="mb-3">
                                                <x-default.label for="amount">Consultation Fee</x-default.label>
                                                <x-default.input name="amount" class="form-control" id="amount" type="text" placeholder="Enter amount (optional)" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="section-box">
                                    <div class="section-header">
                                        <h5><i class="fas fa-user-circle"></i> Patient Information</h5>
                                        <span class="required-badge">Required</span>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <x-default.label required="true" for="patient_name">Full Name</x-default.label>
                                                <x-default.input name="patient_name" class="form-control" id="patient_name" type="text" placeholder="Enter patient's full name" />
                                                <x-default.input-error name="patient_name" />
                                            </div>

                                            <div class="mb-3">
                                                <x-default.label for="patient_phone">Phone Number</x-default.label>
                                                <x-default.input name="patient_phone" class="form-control" id="patient_phone" type="text" placeholder="e.g., 01712345678" />
                                                <div class="form-hint">Format: 01XXXXXXXXX</div>
                                                <x-default.input-error name="patient_phone" />
                                            </div>

                                            <div class="mb-3">
                                                <x-default.label for="patient_email">Email Address</x-default.label>
                                                <x-default.input name="patient_email" class="form-control" id="patient_email" type="email" placeholder="e.g., patient@example.com" />
                                                <x-default.input-error name="patient_email" />
                                            </div>

                                            <div class="mb-3">
                                                <x-default.label for="patient_address">Address</x-default.label>
                                                <textarea name="patient_address" id="patient_address" class="form-control" rows="3" placeholder="Enter patient's address"></textarea>
                                                <x-default.input-error name="patient_address" />
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <x-default.label for="patient_age_year">Age (Years)</x-default.label>
                                                <x-default.input name="patient_age_year" class="form-control" id="patient_age_year" type="text" placeholder="e.g., 25" />
                                            </div>

                                            <div class="mb-3">
                                                <x-default.label for="patient_gender">Gender</x-default.label>
                                                <select name="patient_gender" id="patient_gender" class="form-control">
                                                    <option value="">--Select Gender --</option>
                                                    <option value="male">Male</option>
                                                    <option value="female">Female</option>
                                                    <option value="other">Other</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <x-default.label for="patient_blood_group">Blood Group</x-default.label>
                                                <select name="patient_blood_group" id="patient_blood_group" class="form-control">
                                                    <option value="">--Select Blood Group--</option>
                                                    <option value="A+">A+</option>
                                                    <option value="A-">A-</option>
                                                    <option value="B+">B+</option>
                                                    <option value="B-">B-</option>
                                                    <option value="O+">O+</option>
                                                    <option value="O-">O-</option>
                                                    <option value="AB+">AB+</option>
                                                    <option value="AB-">AB-</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="section-box">
                                    <div class="section-header">
                                        <h5><i class="fas fa-list"></i> Additional Details</h5>
                                    </div>
                                    <div class="mb-3">
                                        <x-default.label for="remarks">Remarks/Notes</x-default.label>
                                        <textarea name="remarks" id="remarks" class="form-control" rows="3" placeholder="Any special notes or remarks about the appointment..."></textarea>
                                    </div>
                                    <div class="mb-2">
                                        <x-default.label for="send_sms">Send SMS Notification</x-default.label>
                                        <div>
                                            <label class="me-3"><input name="send_sms" checked id="send_sms_yes" type="radio" value="yes"/> Yes, send SMS</label>
                                            <label><input name="send_sms" id="send_sms_no" type="radio" value="no"/> No, don't send</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-3">
                                    <a href="{{ route($pageHeader['index_route']) }}" class="btn btn-light">← Cancel</a>
                                    <x-default.button class="btn-success">Create Serial</x-default.button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {

            $('#reefer_id').select2({
                placeholder: "Select a Doctor",
                allowClear: true
            });
        });
    </script>
    <script>
        (function(){
            var nextUrlTemplate = '{{ route("admin.doctor_serials.next", ["reefer" => "__REEFER__"]) }}';

            function fetchAndFill(reeferId) {
                if (!reeferId) {
                    $('#serial_number').val('');
                    $('#serial_help').text('Select a doctor to auto-generate serial.');
                    return;
                }
                var date = $('#date').val();
                var url = nextUrlTemplate.replace('__REEFER__', reeferId) + (date ? ('?date=' + encodeURIComponent(date)) : '');
                $.get(url)
                    .done(function(resp) {
                        if (resp && resp.serial) {
                            if (!$('#serial_manual_toggle').is(':checked')) {
                                $('#serial_number').val(resp.serial);
                            }
                            var help = 'Next serial: #' + resp.serial;
                            if (resp.approx_time) {
                                help += ' — Approx time: ' + resp.approx_time;
                            }
                            $('#serial_help').text(help);
                        }
                    })
                    .fail(function() {
                        $('#serial_help').text('Could not fetch serial. Please try again.');
                    });
            }

            $('#reefer_id').on('change', function() {
                fetchAndFill($(this).val());
            });

            $('#date').on('change', function() {
                fetchAndFill($('#reefer_id').val());
            });

            $('#refresh_serial_btn').on('click', function() {
                fetchAndFill($('#reefer_id').val());
            });

            $('#serial_manual_toggle').on('change', function() {
                var checked = $(this).is(':checked');
                $('#serial_number').prop('readonly', !checked);
                if (checked) {
                    $('#serial_help').text('Manual mode enabled: you can set the serial number.');
                    $('#serial_number').focus();
                } else {
                    $('#serial_help').text('Select a doctor to auto-generate the next serial and see approximate time.');
                    // refresh to show current next serial
                    fetchAndFill($('#reefer_id').val());
                }
            });

            // trigger initial fetch if a doctor is pre-selected
            if ($('#reefer_id').val()) {
                fetchAndFill($('#reefer_id').val());
            }
        })();
    </script>
@endpush
