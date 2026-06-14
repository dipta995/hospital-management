@extends('backend.layouts.master')
@section('title')
    {{ $pageHeader['title'] }}
@endsection

@push('styles')
    @include('backend.layouts.partials.crud-styles')
@endpush

@section('admin-content')
    @php
        $userGuard = Auth::guard('admin')->user();
    @endphp

    <div class="crud-page container-fluid py-3">
        @include('backend.layouts.partials.crud-hero', [
            'heroTitle' => $pageHeader['title'] . ' List',
            'heroSubtitle' => 'Manage expense categories for diagnostic & hospital costs',
            'heroIcon' => 'fa-tags',
            'heroCreateRoute' => $userGuard->can('cost_categories.create') ? $pageHeader['create_route'] : null,
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
                            <th>Type</th>
                            <th>Costs</th>
                            <th class="text-end">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($datas as $item)
                            <tr id="table-data{{ $item->id }}">
                                <td>{{ $loop->iteration + ($datas->currentPage() - 1) * $datas->perPage() }}</td>
                                <td><strong>{{ $item->name }}</strong></td>
                                <td>
                                    @if(($item->type ?? 'diagnostic') === 'hospital')
                                        <span class="crud-badge" style="background:#fef2f2;color:#b91c1c;border-color:rgba(185,28,28,0.15);">Hospital</span>
                                    @else
                                        <span class="crud-badge">Diagnostic</span>
                                    @endif
                                </td>
                                <td>{{ $item->costs_count ?? 0 }}</td>
                                <td class="text-end">
                                    <div class="crud-action-group">
                                        @if($userGuard->can('cost_categories.edit'))
                                            <a href="{{ route($pageHeader['edit_route'], $item->id) }}" class="crud-btn-icon crud-btn-edit" title="Edit">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                        @endif
                                        @if($userGuard->can('cost_categories.delete'))
                                            <a href="javascript:void(0)" class="crud-btn-icon crud-btn-delete" title="Delete"
                                               onclick="dataDelete({{ $item->id }},'{{ $pageHeader['base_url'] }}')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="crud-empty">
                                    No categories found.
                                    @if($userGuard->can('cost_categories.create'))
                                        <a href="{{ route($pageHeader['create_route']) }}" class="btn btn-sm btn-primary ms-2">Create Category</a>
                                    @endif
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
