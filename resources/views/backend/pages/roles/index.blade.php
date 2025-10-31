@extends('backend.layouts.master')
@section('title')
    List Of Role
@endsection
@push('styles')

@endpush
@section('admin-content')
    <!-- partial -->
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">

                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Role's List</h4>
                            <p class="card-description">
                            </p>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Name</th>
                                        <th>Permissions</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($datas as $data)
                                        <tr id="table-data{{ $data->id }}">
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $data->name }}</td>
                                            <td>
                                                @foreach ($data->permissions as $item)
                                                    <span class="badge bg-success">{{ $item->name }}</span>
                                                @endforeach
                                            </td>
                                            <td>

                                                @if (Auth::guard('admin')->user()->can('roles.edit'))
                                                    <a href="{{ route($pageHeader['edit_route'],$data->id) }}"
                                                       class="badge bg-info"><i class="fas fa-pen"></i></a>
                                                @endif
                                                    @if (Auth::guard('admin')->user()->can('roles.delete'))

                                                    <a href="#" class="badge bg-danger"><i class="fas fa-trash"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2"></td>
                                            <td colspan="6">No Data Found ! <a class="btn btn-outline-info"
                                                                               href="{{ $pageHeader['index_route'] }}">Create
                                                    Create New</a>
                                            </td>
                                            <td colspan="2"></td>
                                        </tr>
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
    <!-- main-panel ends -->
@endsection

@push('scripts')

@endpush
