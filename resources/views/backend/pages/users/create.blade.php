@extends('backend.layouts.master')
@section('title')
    Create {{ $pageHeader['title'] }}
@endsection

@push('styles')
    @include('backend.layouts.partials.crud-styles')
@endpush

@section('admin-content')
    <div class="crud-page container-fluid py-3">
        @include('backend.layouts.partials.crud-form-hero', [
            'formTitle' => 'Create Patient',
            'formSubtitle' => 'Register a new patient record',
            'formIcon' => 'fa-user-injured',
        ])

        <div class="crud-card">
            @include('backend.layouts.partials.message')

            <form method="post" action="{{ route($pageHeader['store_route']) }}">
                @csrf

                <div class="crud-form-section">
                    <div class="crud-form-section-header">
                        <i class="fas fa-id-card"></i> Patient Information
                    </div>
                    <div class="crud-form-section-body">
                        <div class="row crud-form-grid g-3">
                            <div class="col-md-4">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input id="name" class="form-control @error('name') is-invalid @enderror" name="name" type="text" value="{{ old('name') }}">
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="phone">Phone <span class="text-danger">*</span></label>
                                <input id="phone" class="form-control @error('phone') is-invalid @enderror" name="phone" type="text" value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="age">Age <span class="text-danger">*</span></label>
                                <input id="age" class="form-control @error('age') is-invalid @enderror" name="age" type="text" value="{{ old('age') }}">
                                @error('age')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="address">Address <span class="text-danger">*</span></label>
                                <input id="address" class="form-control @error('address') is-invalid @enderror" name="address" type="text" value="{{ old('address') }}">
                                @error('address')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <x-default.label required="true" for="blood_group">Blood Group</x-default.label>
                                <select class="form-select" name="blood_group" id="blood_group">
                                    <option value="">-- Select --</option>
                                    @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $group)
                                        <option value="{{ $group }}" @selected(old('blood_group') == $group)>{{ $group }}</option>
                                    @endforeach
                                </select>
                                <x-default.input-error name="blood_group"></x-default.input-error>
                            </div>
                            <div class="col-md-4">
                                <x-default.label required="true" for="gender">Gender</x-default.label>
                                <select class="form-select" name="gender" id="gender">
                                    <option value="Male" @selected(old('gender', 'Male') == 'Male')>Male</option>
                                    <option value="Female" @selected(old('gender') == 'Female')>Female</option>
                                    <option value="Other" @selected(old('gender') == 'Other')>Other</option>
                                </select>
                                <x-default.input-error name="gender"></x-default.input-error>
                            </div>
                            <div class="col-md-4">
                                <label for="marital_status">Marital Status</label>
                                <select class="form-select" name="marital_status" id="marital_status">
                                    <option value="">-- Select --</option>
                                    @foreach(['Single', 'Married', 'Divorced', 'Widowed'] as $status)
                                        <option value="{{ $status }}" @selected(old('marital_status') == $status)>{{ $status }}</option>
                                    @endforeach
                                </select>
                                @error('marital_status')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="occupation">Occupation</label>
                                <input id="occupation" class="form-control" name="occupation" type="text" value="{{ old('occupation') }}">
                                @error('occupation')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="religion">Religion</label>
                                <input id="religion" class="form-control" name="religion" type="text" value="{{ old('religion') }}">
                                @error('religion')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="crud-form-actions">
                    <a href="{{ route($pageHeader['index_route']) }}" class="btn-crud-cancel">Cancel</a>
                    <button type="submit" class="btn btn-crud-submit">Create Patient</button>
                </div>
            </form>
        </div>
    </div>
@endsection
