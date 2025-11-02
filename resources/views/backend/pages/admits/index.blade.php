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
                                        <th>Patient Name</th>
                                        <th>Admit Date</th>
                                        <th>Release Date</th>
                                        <th>NID</th>
                                        <th>Note</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($datas as $data)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ optional($data->user)->name ?? 'Unknown' }}</td>
                                            <td>{{ $data->admit_at ?? 'N/A' }}</td>
                                            <td>
                                                @if(!$data->release_at)
                                                    <button class="btn btn-sm btn-warning add-release-btn"
                                                        data-id="{{ $data->id }}">
                                                        <i class="fas fa-plus"></i> Add Release Date
                                                    </button>
                                                @else
                                                    {{ $data->release_at }}
                                                @endif
                                            </td>
                                            <td>{{ $data->nid ?? 'N/A' }}</td>
                                            <td>{{ Str::limit($data->note, 40) ?? 'N/A' }}</td>
                                            <td>
                                                <a href="{{ route($pageHeader['edit_route'], $data->id) }}" class="badge bg-info">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                                <a href="javascript:void(0)" class="badge bg-danger delete-btn" data-id="{{ $data->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </a>
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

{{-- Release Date Modal --}}
<div class="modal fade" id="releaseModal" tabindex="-1" aria-labelledby="releaseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="releaseModalLabel">Set Release Date</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="releaseDate" class="form-label">Release Date & Time</label>
                <input type="datetime-local" id="releaseDate" class="form-control">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="saveReleaseBtn">Save Release Date</button>
            </div>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    let releaseId = null;
    const releaseModal = new bootstrap.Modal(document.getElementById('releaseModal'));
    const releaseInput = document.getElementById('releaseDate');
    const saveBtn = document.getElementById('saveReleaseBtn');

    // Open modal
    document.querySelectorAll('.add-release-btn').forEach((btn) => {
        btn.addEventListener('click', function() {
            releaseId = this.dataset.id;
            releaseInput.value = ''; // clear previous input
            releaseModal.show();
        });
    });

    // Save release date
    saveBtn.addEventListener('click', function() {
        const releaseDate = releaseInput.value;
        if (!releaseDate) {
            alert("Please select a date and time.");
            return;
        }

        fetch(`/admin/admits/${releaseId}/release`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ release_at: releaseDate })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 200) {
                alert('Release date added successfully!');
                location.reload();
            } else {
                alert('Failed to add release date');
            }
        });
    });

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
