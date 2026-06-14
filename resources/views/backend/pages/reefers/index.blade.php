@extends('backend.layouts.master')
@section('title')
    {{ $pageHeader['title'] }}
@endsection

@push('styles')
    @include('backend.layouts.partials.crud-styles')
@endpush

@section('admin-content')
    <div class="crud-page container-fluid py-3">
        @include('backend.layouts.partials.crud-hero', [
            'heroTitle' => 'Referrers / Doctors',
            'heroSubtitle' => 'Manage referring doctors, commission and contact details',
            'heroIcon' => 'fa-user-md',
            'heroCreateLabel' => 'Add Referrer',
        ])

        <div class="crud-card">
            @include('backend.layouts.partials.message')

            <form action="" method="get" class="crud-toolbar">
                <div class="row g-2 align-items-end flex-grow-1">
                    <div class="col-md-4">
                        <label for="name" class="form-label">Search by Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ request('name') }}" placeholder="Enter referrer name">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </div>
            </form>

            <div class="crud-table-wrap">
                <div class="table-responsive">
                    <table class="table crud-table table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Designation</th>
                            <th>Percent (%)</th>
                            <th>Type</th>
                            <th class="text-end">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($datas as $item)
                            <tr id="table-data{{ $item->id }}">
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $item->name }}</strong></td>
                                <td>{{ $item->phone ?: '—' }}</td>
                                <td>{{ \Illuminate\Support\Str::limit(strip_tags($item->designation ?? ''), 60) ?: '—' }}</td>
                                <td>
                                    <span class="crud-badge">{{ $item->percent }}%</span>
                                    @if($item->customParcent->isNotEmpty())
                                        <div class="mt-2">
                                            @foreach($item->customParcent as $cus)
                                                <div class="small text-muted">
                                                    {{ $cus->category->name ?? 'Category' }}:
                                                    <span class="crud-badge">{{ $cus->percentage }}%</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $item->type ?? 'N/A' }}</td>
                                <td class="text-end">
                                    <div class="crud-action-group">
                                        <a href="{{ route($pageHeader['edit_route'], $item->id) }}" class="crud-btn-icon crud-btn-edit" title="Edit">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <a href="javascript:void(0)" class="crud-btn-icon crud-btn-delete" title="Delete"
                                           onclick="dataDelete({{ $item->id }},'{{ $pageHeader['base_url'] }}')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="crud-empty">
                                    No referrers found.
                                    <a href="{{ route($pageHeader['create_route']) }}" class="btn btn-sm btn-primary ms-2">Add Referrer</a>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {!! $datas->appends(request()->query())->links() !!}
            </div>
        </div>
    </div>
@endsection
