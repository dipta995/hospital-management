@extends('backend.layouts.master')

@section('title')
    All {{ ucfirst($pageHeader['title']) }}
@endsection

@section('admin-content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">

                {{-- Page Header --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">{{ ucfirst($pageHeader['title']) }} List</h4>
                    <a href="{{ route('admin.users.index', ['from' => 'admit']) }}" class="btn btn-success">
                        <i class="fas fa-plus-circle"></i> Admit New
                    </a>
                </div>

                {{-- Messages --}}
                @include('backend.layouts.partials.message')

                {{-- Admit Table --}}
                <div class="card">
                    <div class="card-body bg-white rounded">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Patient</th>
                                        <th>DR.</th>
                                        <th>Father/Spouse</th>
                                        {{-- <th>DR.</th> --}}
                                        <th>Bed/Cabin</th>
                                        <th>Admit Date</th>
                                        <th>Received By</th>
                                        <th>Diagnosis</th>
                                        <th>Note</th>
                                        <th>bed     </th>
                                        <th>Release</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($datas as $data)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ optional($data->user)->name ?? 'Unknown' }}</td>
                                            <td>{{ $data->reefer?->name }}</td>
                                            <td>{{ ($data->father_or_spouse) ?? 'N/A' }}</td>
                                            <td>{{ ($data->bed_or_cabin) ?? 'N/A' }}</td>
                                            <td>{{ $data->admit_at ?? 'N/A' }}</td>
                                            <td>{{ $data->clinical_diagnosis ?? 'N/A' }}</td>
                                             <td>{{ $data->received_by ?? 'N/A' }}</td>
                                            {{-- <td>{{ $data->reefer?->name ?? 'N/A' }}</td> --}}
                                            <td>{{ $data->bed_cabin ?? 'N/A' }}</td>
                                            <td>{{ $data->note ?? 'N/A'}}</td>
                                            <td>
                                                @if(!$data->release_at)
                                                    <a href="{{ route('admin.admits.release.details', $data->id) }}" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-door-open"></i> Release
                                                    </a>
                                                @else
                                                    <div class="d-flex flex-column">
                                                        <span>{{ $data->release_at }}</span>
                                                        <a href="{{ route('admin.admits.release.details', $data->id) }}" target="_blank" class="btn btn-sm btn-outline-info mt-1">
                                                            <i class="fas fa-eye"></i> Preview
                                                        </a>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    @if(!$data->release_at)
                                                        <a href="{{ route('admin.recepts.create').'?admitId='.$data->id .'&for='.$data->user_id }}" class="btn btn-sm btn-info text-white" title="Create Receipt">
                                                            <i class="fas fa-file-invoice-dollar"></i>
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('admin.recepts.index').'?for='.$data->id }}" class="btn btn-sm btn-warning text-white" title="View Receipts">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.admits.print', $data->id) }}" class="btn btn-sm btn-secondary" title="Print Patient Details" target="_blank">
                                                        <i class="fas fa-print"></i>
                                                    </a>
                                                    @if(!$data->release_at)
                                                        <a href="{{ route($pageHeader['edit_route'], $data->id) }}" class="btn btn-sm btn-primary" title="Edit Admit">
                                                            <i class="fas fa-pen"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $data->id }}" title="Delete Admit">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="bg-white">
                                            <td colspan="7" class="text-center">No admits found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            {{-- Pagination --}}
                            <div class="d-flex justify-content-end mt-3">
                                {!! $datas->links() !!}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete button
    document.querySelectorAll('.delete-btn').forEach((btn) => {
        btn.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete this admit?')) {
                let id = this.dataset.id;
                fetch(`/admin/admits/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 200) {
                        alert('Admit deleted successfully');
                        location.reload();
                    } else {
                        alert('Failed to delete admit');
                    }
                });
            }
        });
    });
});
</script>
@endsection
