@extends('backend.layouts.master')

@section('title')
    Edit {{ $pageHeader['title'] }}
@endsection

@section('admin-content')
<div class="container mt-5">
    <h2 class="mb-4">Edit {{ $pageHeader['title'] }}</h2>

    @include('backend.layouts.partials.message')

    <form action="{{ route($pageHeader['update_route'], $prescription->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="reefer_id" class="form-label">Select Doctor <span class="text-danger">*</span></label>
            <select name="reefer_id" id="reefer_id" class="form-control" required>
                <option value="">-- Choose Doctor --</option>
                @foreach($reefers as $reefer)
                    <option value="{{ $reefer->id }}" {{ $prescription->reefer_id == $reefer->id ? 'selected' : '' }}>
                        {{ $reefer->name }}
                    </option>
                @endforeach
            </select>
            @error('reefer_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="diagnosis" class="form-label">Diagnosis</label>
            <input type="text" name="diagnosis" id="diagnosis" class="form-control" value="{{ old('diagnosis', $prescription->diagnosis) }}">
            @error('diagnosis')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="investigation" class="form-label">Investigation</label>
            <input type="text" name="investigation" id="investigation" class="form-control" value="{{ old('investigation', $prescription->investigation) }}">
            @error('investigation')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <hr>
        <h4>Drugs</h4>

        @foreach($prescription->drugs as $index => $drug)
            <div class="card mb-3 p-3">
                <div class="mb-2">
                    <label class="form-label">Drug Name</label>
                    <input type="text" name="drug_name[]" class="form-control" value="{{ $drug->name }}">
                </div>
                <div class="mb-2">
                    <label class="form-label">Rule</label>
                    <input type="text" name="drug_rule[]" class="form-control" value="{{ $drug->rule }}">
                </div>
                <div class="mb-2">
                    <label class="form-label">Time</label>
                    <input type="text" name="drug_time[]" class="form-control" value="{{ $drug->time }}">
                </div>
                <div class="mb-2">
                    <label class="form-label">Note</label>
                    <input type="text" name="drug_note[]" class="form-control" value="{{ $drug->note }}">
                </div>
                <div class="mb-2">
                    <label class="form-label">Duration</label>
                    <input type="text" name="drug_duration[]" class="form-control" value="{{ $drug->duration }}">
                </div>
            </div>
        @endforeach

        <button type="submit" class="btn btn-success">Update Prescription</button>
        <a href="{{ route('admin.prescriptions.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
