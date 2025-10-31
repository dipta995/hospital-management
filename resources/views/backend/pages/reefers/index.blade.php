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
                            <h4 class="card-title">{{ $pageHeader['title'] }}'s List</h4>
                            <p class="card-description">
                                @include('backend.layouts.partials.message')
                            </p>
                            <form action="" method="get" class="row">
                                <div class="form-group col-md-3">
                                    <input type="text" name="name" class="form-control">
                                </div>
                                <div class="form-group col-md-3">
                                    <button class="btn btn-info">Search</button>
                                </div>
                            </form>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Designation</th>
                                        <th>Percent (%)</th>
                                        <th>Type</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($datas as $item)
                                        <tr id="table-data{{ $item->id }}">
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->phone }}</td>
                                            <td>{!! $item->designation !!}</td>
                                            <td>{{ $item->percent }} %
                                                <hr>
                                                @foreach($item->customParcent as $cus)
                                                    <strong>{{ $cus->category->name }} <span class="badge bg-info">{{ $cus->percentage }} %</span></strong>
                                                @endforeach
                                            </td>
                                            <td><strong>{{ $item->type ?? 'N/a' }}</strong></td>
                                            <td>
                                                <a href="{{ route($pageHeader['edit_route'],$item->id) }}"
                                                   class="badge bg-info"><i class="fas fa-pen"></i></a>
                                                <a class="badge bg-danger" href="javascript:void(0)"
                                                   onclick="dataDelete({{ $item->id }},'{{ $pageHeader['base_url'] }}')"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td>No record Found <a href="{{ route($pageHeader['create_route']) }}"
                                                                   class="btn btn-info">Create</a></td>
                                        </tr>
                                    @endforelse

                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-end">
                                    {!! $datas->appends(request()->query())->links() !!}
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
