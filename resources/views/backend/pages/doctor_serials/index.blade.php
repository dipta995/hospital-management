@extends('backend.layouts.master')

@section('title')
    List of {{ $pageHeader['title'] }}'s
@endsection

@push('styles')
    <!-- Add Select2 CSS for better dropdown UI -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <style>
        .form-group {
            margin-bottom: 15px;
        }

        .form-control, .form-control-sm {
            font-size: 14px;
            padding: 10px;
            border-radius: 4px;
            width: 100%;
        }

        .form-group .form-control {
            height: 40px;
        }

        .btn {
            padding: 10px 15px;
            font-size: 14px;
            border-radius: 4px;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }

        select.form-control.form-control-sm {
            height: 40px;
        }
    </style>
@endpush

@section('admin-content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">{{ $pageHeader['title'] }}'s List</h4>
                            <p class="card-description">
                                @include('backend.layouts.partials.message')
                            </p>

                            <form method="get" class="row gx-2 gy-2 align-items-end" style="margin-top: 10px; margin-bottom: 20px;">

                                <div class="col-md-3">
                                    <div class="form-group mb-0">
                                        <x-default.label required="true" for="reefer_id">Reefer Type</x-default.label>
                                        <select name="reefer_id" id="reefer_id"
                                                style="height: 50px; font-size: 14px; padding: 10px; border-radius: 4px; width: 100%;"
                                                class="form-control">
                                            <option value="">Choose Reefer</option>
                                            @foreach($reefers as $reefer)
                                                <option value="{{ $reefer->id }}" @if(request('reefer_id') == $reefer->id) selected @endif>
                                                    {{ $reefer->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <div class="col-md-3">
                                    <div class="form-group mb-0">
                                        <x-default.label required="true" for="date">Date</x-default.label>
                                        <x-text-input
                                            value="{{ request('date') }}"
                                            type="date"
                                            name="date"
                                            class="form-control form-control-sm"
                                            style="height: 40px; font-size: 14px; padding: 10px; border-radius: 4px; width: 100%;"
                                        />
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group mb-0">
                                        <label for="export">Export</label>
                                        <select name="export"
                                                style="height: 40px; font-size: 14px; padding: 10px; border-radius: 4px; width: 100%;"
                                                class="form-control form-control-sm">
                                            <option value="">No</option>
                                            <option value="pdf" @if(request('export') == 'pdf') selected @endif>PDF</option>
                                        </select>
                                    </div>
                                </div>


                                <div class="col-md-3 d-flex justify-content-end gap-1">
                                    <button type="submit"
                                            class="btn btn-primary btn-sm"
                                            style="height: 40px; font-size: 14px; padding: 10px 15px; border-radius: 4px;">
                                        Search
                                    </button>
                                    <a href="{{ route($pageHeader['index_route']) }}"
                                       class="btn btn-secondary btn-sm"
                                       style="height: 40px; font-size: 14px; padding: 10px 15px; border-radius: 4px;">
                                        Reset
                                    </a>
                                </div>
                            </form>



                            {{-- <!-- Print Button -->
                            <a href="{{ route('admin.doctor_serials.print', ['reefer_id' => request('reefer_id'), 'date' => request('date')]) }}" class="btn btn-success btn-sm">
                                Print PDF
                            </a> --}}

                            <!-- Data Table (grouped by doctor for clarity) -->
                            <div class="table-responsive mt-3">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Doctor</th>
                                            <th>Date</th>
                                            <th>Patient Name</th>
                                            <th>Serial Number</th>
                                            <th>ETA</th>
                                            <th>remarks</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $groups = $datas->groupBy('reefer_id'); $rowIndex = 0; @endphp
                                        @forelse($groups as $reeferId => $group)
                                            @php
                                                $doctor = optional($group->first()->doctor);
                                                $dateForGroup = optional($group->first())->date ?? now()->toDateString();
                                                // compute next serial for display
                                                $maxSerial = $group->map(function($g){ return (int) $g->serial_number; })->max();
                                                $nextSerial = $maxSerial ? $maxSerial + 1 : 1;
                                                $pendingCount = $group->where('status', 'Pending')->count();
                                                $checking = $group->firstWhere('status', 'Checking');
                                                $startTime = $doctor && $doctor->office_time ? \Carbon\Carbon::parse($doctor->office_time) : null;
                                                $approxNextTime = null;
                                                if ($startTime) {
                                                    $minutesToAdd = ($nextSerial - 1) * 3;
                                                    $approxNextTime = $startTime->copy()->addMinutes($minutesToAdd)->format('g:i A');
                                                }
                                            @endphp
                                            <tr class="table-primary">
                                                <td colspan="9" style="font-weight:700;">
                                                    <div style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
                                                        <div>
                                                            <i class="fas fa-user-md"></i> {{ $doctor->name ?? 'Unknown Doctor' }}
                                                            <small class="text-muted"> &middot; {{ $group->count() }} total</small>
                                                        </div>
                                                        <div>
                                                            <span class="badge bg-light text-dark">Next: #{{ $nextSerial }}</span>
                                                            <span class="badge bg-info text-white">Pending: {{ $pendingCount }}</span>
                                                            @if($approxNextTime)
                                                                <span class="badge bg-success">Approx: {{ $approxNextTime }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            @foreach($group->sortBy(function($r){ return (int)$r->serial_number; }) as $item)
                                                @php $rowIndex++; @endphp
                                                <tr id="table-data{{ $item->id }}" @if(strtolower($item->status)=='checking') style="background:#fff3cd" @endif>
                                                    <td>{{ $rowIndex }}</td>
                                                    <td>{{ $doctor->name ?? '—' }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($item->date)->format('Y-m-d') }}</td>
                                                    <td>{{ $item->patient_name }}</td>
                                                    <td>{{ $item->serial_number }}</td>
                                                    <td>
                                                        @php
                                                            $eta = null;
                                                            if ($startTime) {
                                                                $minutesToAdd = ((int)$item->serial_number - 1) * 3;
                                                                $eta = $startTime->copy()->addMinutes($minutesToAdd)->format('g:i A');
                                                            }
                                                        @endphp
                                                        {{ $eta ?? '—' }}
                                                    </td>
                                                    <td>{{ $item->remarks }}</td>
                                                    <td>
                                                        @php
                                                            $status = strtolower($item->status ?? 'pending');
                                                            $class = 'badge bg-secondary';
                                                            if ($status === 'checking') $class = 'badge bg-warning';
                                                            if ($status === 'complete') $class = 'badge bg-success';
                                                            if ($status === 'rejected') $class = 'badge bg-danger';
                                                        @endphp
                                                        <span class="{{ $class }}">{{ $item->status }}</span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route($pageHeader['edit_route'], $item->id) }}" class="badge bg-info"><i class="fas fa-pen"></i></a>
                                                        <a class="badge bg-danger" href="javascript:void(0)" onclick="dataDelete({{ $item->id }},'{{ $pageHeader['base_url'] }}')"><i class="fas fa-trash"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @empty
                                            <tr>
                                                <td colspan="9">No records found. <a href="{{ route($pageHeader['create_route']) }}" class="btn btn-info">Create</a></td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <!-- Pagination Links -->
                                {{-- pagination omitted for grouped view --}}
                            </div>
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
            placeholder: "Select a Reefer",
            allowClear: true
        });
    });

    function dataDelete(id, baseUrl) {
        if (confirm("Are you sure you want to delete this record?")) {
            $.ajax({
                url: baseUrl + '/' + id,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.status === 200) {
                        $('#table-data' + id).remove();
                        alert('Deleted successfully!');
                    } else {
                        alert('Delete failed. Please try again.');
                    }
                },
                error: function () {
                    alert('Something went wrong!');
                }
            });
        }
    }
</script>
@endpush
