@extends('backend.layouts.master')

@section('title')
    {{ $pageHeader['title'] }}
@endsection

@push('styles')
    @include('backend.layouts.partials.crud-styles')
    @include('backend.layouts.partials.cost-category-select2-assets')
    <style>
        .ds-group-row td {
            background: linear-gradient(90deg, #ecfdf5, #f0fdfa);
            font-weight: 700;
            border-top: 2px solid #99f6e4;
        }
        .ds-row-checking { background: #fffbeb !important; }
        .ds-row-complete { opacity: 0.75; }
        .ds-status-pending { background: #f1f5f9; color: #475569; }
        .ds-status-checking { background: #fef3c7; color: #b45309; }
        .ds-status-complete { background: #dcfce7; color: #15803d; }
        .ds-status-rejected { background: #fee2e2; color: #b91c1c; }
        .ds-stat-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
            background: #fff;
            border: 1px solid #cbd5e1;
            color: #334155;
        }
    </style>
@endpush

@section('admin-content')
    @php
        $userGuard = Auth::guard('admin')->user();
    @endphp

    <div class="crud-page container-fluid py-3">
        @include('backend.layouts.partials.crud-hero', [
            'heroTitle' => 'Doctor Serial Queue',
            'heroSubtitle' => \Carbon\Carbon::parse($selectedDate)->format('l, d M Y') . ' · Manage patient serials & status',
            'heroIcon' => 'fa-user-md',
            'heroCreateRoute' => $userGuard->can('doctor_serials.create') ? $pageHeader['create_route'] : null,
            'heroCreateLabel' => 'New Serial',
        ])

        <div class="crud-card">
            @include('backend.layouts.partials.message')

            <form method="get" class="crud-toolbar">
                <div class="row g-2 flex-grow-1 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label" for="reefer_id">Doctor</label>
                        <select name="reefer_id" id="reefer_id" class="form-select cost-category-select" data-placeholder="All doctors">
                            <option value="">All doctors</option>
                            @foreach($reefers as $reefer)
                                <option value="{{ $reefer->id }}" @selected((string) $selectedReeferId === (string) $reefer->id)>
                                    {{ $reefer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="date">Date</label>
                        <input type="date" name="date" id="date" class="form-control"
                               value="{{ $selectedDate }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="export">Export</label>
                        <select name="export" id="export" class="form-select">
                            <option value="">No</option>
                            <option value="pdf" @selected(request('export') === 'pdf')>PDF</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Search</button>
                        <a href="{{ route($pageHeader['index_route']) }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </div>
            </form>

            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="ds-stat-pill"><i class="fas fa-list-ol"></i> Total: {{ $totalSerials ?? 0 }}</span>
                <span class="ds-stat-pill"><i class="fas fa-clock"></i> Pending: {{ $pendingCount ?? 0 }}</span>
            </div>

            <div class="crud-table-wrap">
                <div class="table-responsive">
                    <table class="table crud-table table-hover mb-0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Serial</th>
                            <th>Patient</th>
                            <th>Phone</th>
                            <th>ETA</th>
                            <th>Remarks</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $groups = $datas->groupBy('reefer_id'); $rowIndex = 0; @endphp
                        @forelse($groups as $reeferId => $group)
                            @php
                                $doctor = optional($group->first()->doctor);
                                $startTime = $doctor && $doctor->office_time ? \Carbon\Carbon::parse($doctor->office_time) : null;
                                $maxSerial = $group->max(fn ($g) => (int) $g->serial_number);
                                $nextSerial = ($maxSerial ?? 0) + 1;
                                $pendingInGroup = $group->where('status', 'Pending')->count();
                                $approxNextTime = null;
                                if ($startTime) {
                                    $approxNextTime = $startTime->copy()->addMinutes(($nextSerial - 1) * 3)->format('g:i A');
                                }
                            @endphp
                            <tr class="ds-group-row">
                                <td colspan="8">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                        <div>
                                            <i class="fas fa-user-md text-success"></i>
                                            {{ $doctor->name ?? 'Unknown Doctor' }}
                                            <small class="text-muted">· {{ $group->count() }} patient(s)</small>
                                        </div>
                                        <div class="d-flex flex-wrap gap-2">
                                            <span class="ds-stat-pill">Next #{{ $nextSerial }}</span>
                                            <span class="ds-stat-pill">Pending {{ $pendingInGroup }}</span>
                                            @if($approxNextTime)
                                                <span class="ds-stat-pill"><i class="far fa-clock"></i> ~{{ $approxNextTime }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            @foreach($group->sortBy(fn ($r) => (int) $r->serial_number) as $item)
                                @php
                                    $rowIndex++;
                                    $statusKey = strtolower($item->status ?? 'pending');
                                    $rowClass = $statusKey === 'checking' ? 'ds-row-checking' : ($statusKey === 'complete' ? 'ds-row-complete' : '');
                                    $eta = null;
                                    if ($startTime) {
                                        $eta = $startTime->copy()->addMinutes(((int) $item->serial_number - 1) * 3)->format('g:i A');
                                    }
                                @endphp
                                <tr id="table-data{{ $item->id }}" class="{{ $rowClass }}">
                                    <td>{{ $rowIndex }}</td>
                                    <td><strong>#{{ $item->serial_number }}</strong></td>
                                    <td>
                                        <div class="fw-semibold">{{ $item->patient_name }}</div>
                                        @if($item->patient_age_year)
                                            <small class="text-muted">Age {{ $item->patient_age_year }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $item->patient_phone ?: '—' }}</td>
                                    <td>{{ $eta ?? '—' }}</td>
                                    <td>{{ $item->remarks ?: '—' }}</td>
                                    <td>
                                        <span class="crud-badge ds-status-{{ $statusKey }}">{{ $item->status }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="crud-action-group">
                                            @if($userGuard->can('doctor_serials.edit'))
                                                <a href="{{ route($pageHeader['edit_route'], $item->id) }}" class="crud-btn-icon crud-btn-edit" title="Edit">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                            @endif
                                            @if($userGuard->can('doctor_serials.delete'))
                                                <a href="javascript:void(0)" class="crud-btn-icon crud-btn-delete" title="Delete"
                                                   onclick="dataDelete({{ $item->id }},'{{ $pageHeader['base_url'] }}')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="8" class="crud-empty">
                                    No serials for this date.
                                    @if($userGuard->can('doctor_serials.create'))
                                        <a href="{{ route($pageHeader['create_route']) }}" class="btn btn-sm btn-primary ms-2">Create Serial</a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#reefer_id').select2({ placeholder: 'All doctors', allowClear: true, width: '100%', minimumResultsForSearch: 0 });
        });
    </script>
@endpush
