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
            'heroTitle' => $pageHeader['title'] . ' List',
            'heroSubtitle' => 'Manage lab test categories and room details',
            'heroIcon' => 'fa-layer-group',
            'heroCreateLabel' => 'Add Category',
        ])

        <div class="crud-card">
            @include('backend.layouts.partials.message')

            <div class="crud-table-wrap">
                <div class="table-responsive">
                    <table class="table crud-table table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Room No</th>
                            <th>Room Name</th>
                            <th class="text-end">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($datas as $item)
                            <tr id="table-data{{ $item->id }}">
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $item->name }}</strong></td>
                                <td>{{ $item->room_no ?: '—' }}</td>
                                <td>{{ $item->room_name ?: '—' }}</td>
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
                                <td colspan="5" class="crud-empty">
                                    No categories found.
                                    <a href="{{ route($pageHeader['create_route']) }}" class="btn btn-sm btn-primary ms-2">Create Category</a>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {!! $datas->links() !!}
            </div>
        </div>
    </div>
@endsection
