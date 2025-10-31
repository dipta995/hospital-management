@extends('backend.layouts.master')
@section('title')
    List of {{ $pageHeader['title'] }}'s

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
                            <h4 class="card-title">{{ $pageHeader['title'] }}   's List</h4>
                            <p class="card-description">
                                @include('backend.layouts.partials.message')
                            </p>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Branch</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($datas as $item)
                                    <tr id="table-data{{ $item->id }}">
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->email }}</td>
                                        <td>{{ $item->branch->name }}</td>
                                        <td>
                                            @foreach ($item->roles as $role)
                                                <span class="badge bg-success">{{ $role->name }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            <a href="{{ route($pageHeader['edit_route'],$item->id) }}" class="badge bg-info">Edit</a>
                                            <a href="" class="badge bg-danger">Delete</a>
                                        </td>
                                    </tr>
                                    @empty
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td >No record Found <x-default.attribute href="{{ route($pageHeader['index_route']) }}"></x-default.attribute></td>
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
