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
            'heroTitle' => 'Lab Tests / Products',
            'heroSubtitle' => 'Manage diagnostic tests, pricing and referral fees',
            'heroIcon' => 'fa-flask',
            'heroCreateLabel' => 'Add Test',
        ])

        <div class="crud-card">
            @include('backend.layouts.partials.message')

            <form action="" method="get" class="crud-toolbar">
                <div class="row g-2 align-items-end flex-grow-1">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Test Name / Code</label>
                        <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Search by name or code">
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </div>
            </form>

            <div class="crud-table-wrap">
                <div class="table-responsive">
                    <table class="table crud-table table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Test</th>
                            <th>Code</th>
                            <th>Price</th>
                            <th>Refer Fee</th>
                            <th>Category</th>
                            <th class="text-end">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($datas as $key => $item)
                            <tr id="table-data{{ $item->id }}">
                                <td>{{ \App\Helper\CustomHelper::startIndexDynamic($datas) + $key }}</td>
                                <td><strong>{{ $item->name }}</strong></td>
                                <td><span class="crud-badge">{{ $item->code }}</span></td>
                                <td>{{ number_format($item->price, 2) }}</td>
                                <td>{{ number_format($item->reefer_fee, 2) }}</td>
                                <td>{{ $item->category->name ?? '—' }}</td>
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
                                    No tests found.
                                    <a href="{{ route($pageHeader['create_route']) }}" class="btn btn-sm btn-primary ms-2">Add Test</a>
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
