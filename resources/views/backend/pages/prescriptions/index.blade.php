@extends('backend.layouts.master')

@section('title')
    All {{ $pageHeader['title'] }}
@endsection

@section('admin-content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">All {{ $pageHeader['title'] }}</h4>
                        @include('backend.layouts.partials.message')

                        <div class="mb-3 text-end">
                            <a href="{{ route($pageHeader['create_route']) }}" class="btn btn-primary">+ Add New</a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Doctor</th>
                                        <th>Investigation</th>
                                        <th>Diagnosis</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($prescriptions as $index => $prescription)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $prescription->doctor->name ?? 'N/A' }}</td>
        <td>{{ !empty($prescription->investigation) ? Str::limit($prescription->investigation, 30) : 'N/A' }}</td>
        <td>{{ !empty($prescription->diagnosis) ? Str::limit($prescription->diagnosis, 30) : 'N/A' }}</td>
        <td>{{ $prescription->created_at->format('d M Y') }}</td>
        <td>
            <a href="{{ route($pageHeader['show_route'], $prescription->id) }}" class="btn btn-info btn-sm">View</a>
            <a href="{{ route($pageHeader['edit_route'], $prescription->id) }}" class="btn btn-warning btn-sm">Edit</a>
            <form action="{{ route($pageHeader['delete_route'], $prescription->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
            </form>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center">No prescriptions found.</td>
    </tr>
@endforelse

                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-3">
                            {{ $prescriptions->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>    
@endsection
