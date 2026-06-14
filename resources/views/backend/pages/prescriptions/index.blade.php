@extends('backend.layouts.master')

@section('title')
    {{ $pageHeader['title'] }}
@endsection

@push('styles')
    @include('backend.layouts.partials.crud-styles')
    @include('backend.layouts.partials.cost-category-select2-assets')
@endpush

@section('admin-content')
    @php $userGuard = Auth::guard('admin')->user(); @endphp

    <div class="crud-page container-fluid py-3">
        @include('backend.layouts.partials.crud-hero', [
            'heroTitle' => 'Prescriptions',
            'heroSubtitle' => $linkedDoctor ? 'Your prescriptions · ' . $linkedDoctor->name : 'All branch prescriptions',
            'heroIcon' => 'fa-prescription',
            'heroCreateRoute' => $userGuard->can('prescriptions.create') ? $pageHeader['create_route'] : null,
            'heroCreateLabel' => 'New Prescription',
        ])

        <div class="crud-card">
            @include('backend.layouts.partials.message')

            @if(empty($linkedDoctor) && $doctors->count())
                <form method="get" class="crud-toolbar">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label" for="doctor_id">Doctor</label>
                            <select name="doctor_id" id="doctor_id" class="form-select cost-category-select" data-placeholder="All doctors">
                                <option value="">All doctors</option>
                                @foreach($doctors as $doc)
                                    <option value="{{ $doc->id }}" @selected(request('doctor_id') == $doc->id)>{{ $doc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="search">Search</label>
                            <input type="text" name="search" id="search" class="form-control"
                                   value="{{ request('search') }}" placeholder="Patient, diagnosis, doctor...">
                        </div>
                        <div class="col-md-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route($pageHeader['index_route']) }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </div>
                </form>
            @endif

            <div class="crud-table-wrap">
                <div class="table-responsive">
                    <table class="table crud-table table-hover mb-0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Diagnosis</th>
                            <th>Investigation</th>
                            <th>Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($prescriptions as $index => $prescription)
                            <tr>
                                <td>{{ $prescriptions->firstItem() + $index }}</td>
                                <td>
                                    <strong>{{ $prescription->invoice->patient_name ?? 'N/A' }}</strong>
                                    @if($prescription->invoice?->patient_age_year)
                                        <div><small class="text-muted">Age {{ $prescription->invoice->patient_age_year }}</small></div>
                                    @endif
                                </td>
                                <td>{{ $prescription->doctor->name ?? 'N/A' }}</td>
                                <td>{{ $prescription->diagnosis ? Str::limit($prescription->diagnosis, 40) : '—' }}</td>
                                <td>{{ $prescription->investigation ? Str::limit($prescription->investigation, 40) : '—' }}</td>
                                <td>{{ $prescription->created_at->format('d M Y') }}</td>
                                <td class="text-end">
                                    <div class="crud-action-group">
                                        <a href="{{ route($pageHeader['show_route'], $prescription->id) }}" class="crud-btn-icon crud-btn-view" title="View / Print">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($userGuard->can('prescriptions.edit'))
                                            <a href="{{ route($pageHeader['edit_route'], $prescription->id) }}" class="crud-btn-icon crud-btn-edit" title="Edit">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                        @endif
                                        @if($userGuard->can('prescriptions.delete'))
                                            <form action="{{ route($pageHeader['delete_route'], $prescription->id) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Delete this prescription?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="crud-btn-icon crud-btn-delete border-0" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="crud-empty">
                                    No prescriptions found.
                                    @if($userGuard->can('prescriptions.create'))
                                        <a href="{{ route($pageHeader['create_route']) }}" class="btn btn-sm btn-primary ms-2">Create Prescription</a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $prescriptions->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#doctor_id').select2({ placeholder: 'All doctors', allowClear: true, width: '100%', minimumResultsForSearch: 0 });
        });
    </script>
@endpush
