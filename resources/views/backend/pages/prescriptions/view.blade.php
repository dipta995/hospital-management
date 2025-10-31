@extends('backend.layouts.master')

@section('title')
    View {{ $pageHeader['title'] }}
@endsection

@section('admin-content')
<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h3>Prescription Details</h3>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <strong>Doctor:</strong>
                <p>{{ $prescription->reefer->name ?? 'N/A' }}</p>
            </div>

            <div class="mb-3">
                <strong>Diagnosis:</strong>
                <p>{{ $prescription->diagnosis ?? 'N/A' }}</p>
            </div>

            <div class="mb-3">
                <strong>Investigation:</strong>
                <p>{{ $prescription->investigation ?? 'N/A' }}</p>
            </div>

            <div class="mb-3">
                <strong>Created At:</strong>
                <p>{{ $prescription->created_at->format('d M Y') }}</p>
            </div>

            <hr>

            <h4>Drugs</h4>
            @forelse($prescription->drugs as $drug)
                <div class="card mb-3 p-3">
                    <p><strong>Name:</strong> {{ $drug->name }}</p>
                    <p><strong>Rule:</strong> {{ $drug->rule }}</p>
                    <p><strong>Time:</strong> {{ $drug->time }}</p>
                    <p><strong>Note:</strong> {{ $drug->note }}</p>
                    <p><strong>Duration:</strong> {{ $drug->duration }}</p>
                </div>
            @empty
                <p>No drugs found for this prescription.</p>
            @endforelse

            <a href="{{ route('admin.prescriptions.index') }}" class="btn btn-secondary mt-3">Back to List</a>
        </div>
    </div>
</div>
@endsection
