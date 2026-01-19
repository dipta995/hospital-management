@extends('backend.layouts.master')

@section('title')
    List of {{ $pageHeader['title'] }}
@endsection

@section('admin-content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{ $pageHeader['title'] }} List</h4>
                        @include('backend.layouts.partials.message')
                        <div class="mb-3 text-end">
                            <a href="{{ route($pageHeader['create_route']) }}" class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i> Add Bed/Cabin
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name/Number</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Price</th>
                                        <th>Note</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($datas as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td class="text-capitalize">{{ $item->type }}</td>
                                            <td class="text-capitalize">{{ $item->status }}</td>
                                            <td>{{ $item->price }}</td>
                                            <td>{{ $item->note }}</td>
                                            <td>
                                                <a href="{{ route($pageHeader['edit_route'], $item->id) }}" class="badge bg-info"><i class="fas fa-pen"></i></a>
                                                <a href="javascript:void(0)" class="badge bg-danger" onclick="dataDelete({{ $item->id }}, '{{ $pageHeader['base_url'] }}')"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center">No record found</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-end">
                                {!! $datas->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
