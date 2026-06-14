@extends('backend.layouts.master')

@section('title')
    Edit {{ $pageHeader['title'] }}
@endsection

@push('styles')
    @include('backend.layouts.partials.crud-styles')
@endpush

@section('admin-content')
    <div class="crud-page container-fluid py-3">
        @include('backend.layouts.partials.crud-form-hero', [
            'formTitle' => 'Edit Serial #' . $edited->serial_number,
            'formSubtitle' => ($edited->doctor->name ?? 'Doctor') . ' · ' . $edited->patient_name,
            'formIcon' => 'fa-pen-to-square',
        ])

        <div class="crud-card">
            @include('backend.layouts.partials.message')

            <form method="post" action="{{ route($pageHeader['update_route'], $edited->id) }}">
                @method('PUT')
                @csrf

                <div class="crud-form-section">
                    <div class="crud-form-section-header"><i class="fas fa-info-circle"></i> Appointment Details</div>
                    <div class="crud-form-section-body">
                        <div class="row crud-form-grid g-3">
                            <div class="col-md-4">
                                <label class="form-label">Doctor</label>
                                <input type="text" class="form-control" readonly
                                       value="{{ $edited->doctor->name ?? 'N/A' }}">
                            </div>
                            <div class="col-md-4">
                                <x-default.label required="true" for="serial_number">Serial Number</x-default.label>
                                <input name="serial_number" class="form-control" id="serial_number" type="text"
                                       value="{{ old('serial_number', $edited->serial_number) }}" required>
                                <x-default.input-error name="serial_number" />
                            </div>
                            <div class="col-md-4">
                                <x-default.label required="true" for="date">Date</x-default.label>
                                <input id="date" class="form-control" name="date" type="date"
                                       value="{{ old('date', \Carbon\Carbon::parse($edited->date)->format('Y-m-d')) }}" required>
                                @error('date')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <x-default.label required="true" for="status">Status</x-default.label>
                                <select name="status" id="status" class="form-select" required>
                                    @foreach($statusOptions as $status)
                                        <option value="{{ $status }}" @selected(old('status', $edited->status) === $status)>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <x-default.label for="amount">Fee (৳)</x-default.label>
                                <input name="amount" class="form-control" id="amount" type="number" step="0.01"
                                       value="{{ old('amount', $edited->amount) }}">
                            </div>
                            <div class="col-md-4">
                                <x-default.label for="remarks">Remarks</x-default.label>
                                <input name="remarks" class="form-control" id="remarks" type="text"
                                       value="{{ old('remarks', $edited->remarks) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="crud-form-section">
                    <div class="crud-form-section-header"><i class="fas fa-user"></i> Patient</div>
                    <div class="crud-form-section-body">
                        <div class="row crud-form-grid g-3">
                            <div class="col-md-6">
                                <x-default.label required="true" for="patient_name">Patient Name</x-default.label>
                                <input name="patient_name" class="form-control" id="patient_name" type="text"
                                       value="{{ old('patient_name', $edited->patient_name) }}" required>
                                <x-default.input-error name="patient_name" />
                            </div>
                            <div class="col-md-3">
                                <x-default.label for="patient_phone">Phone</x-default.label>
                                <input name="patient_phone" class="form-control" id="patient_phone" type="text"
                                       value="{{ old('patient_phone', $edited->patient_phone) }}">
                                <x-default.input-error name="patient_phone" />
                            </div>
                            <div class="col-md-3">
                                <x-default.label for="patient_age_year">Age</x-default.label>
                                <input name="patient_age_year" class="form-control" id="patient_age_year" type="text"
                                       value="{{ old('patient_age_year', $edited->patient_age_year) }}">
                            </div>
                            <div class="col-md-4">
                                <x-default.label for="patient_gender">Gender</x-default.label>
                                <select name="patient_gender" id="patient_gender" class="form-select">
                                    <option value="">Select</option>
                                    @foreach(['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $val => $label)
                                        <option value="{{ $val }}" @selected(old('patient_gender', $edited->patient_gender) === $val)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <x-default.label for="patient_blood_group">Blood Group</x-default.label>
                                <input name="patient_blood_group" class="form-control" id="patient_blood_group" type="text"
                                       value="{{ old('patient_blood_group', $edited->patient_blood_group) }}">
                            </div>
                            <div class="col-md-4">
                                <x-default.label for="patient_email">Email</x-default.label>
                                <input name="patient_email" class="form-control" id="patient_email" type="email"
                                       value="{{ old('patient_email', $edited->patient_email) }}">
                            </div>
                            <div class="col-12">
                                <x-default.label for="patient_address">Address</x-default.label>
                                <textarea name="patient_address" id="patient_address" class="form-control" rows="2">{{ old('patient_address', $edited->patient_address) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="crud-form-actions">
                    <a href="{{ route($pageHeader['index_route'], ['date' => $edited->date, 'reefer_id' => $edited->reefer_id]) }}" class="btn-crud-cancel">Cancel</a>
                    <button type="submit" class="btn btn-crud-submit">Update Serial</button>
                </div>
            </form>
        </div>
    </div>
@endsection
