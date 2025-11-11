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
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Customer</th>
                                        <th>Total||Discount</th>
                                        <th>Due</th>
                                        <th>Created Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($datas as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->user->name ?? '' }}</td>
                                            <td>{{ $item->total_amount }}</td>
                                            <td><strong class="text-danger">{{ $item->discount_amount }}</strong></td>
                                            <td>{{ $item->receptPayments->sum('paid_amount') }}
                                            </td>
                                            <td>
                                                <a target="_blank"
                                                   href="{{ route('admin.recepts.pdf-preview',$item->id) }}"
                                                   class="badge bg-danger"><i class="fas fa-file-pdf"></i></a>
                                                <br>
                                                <a href="{{ route($pageHeader['edit_route'], $item->id) }}" class="badge bg-info"><i class="fas fa-pen"></i></a>
                                                <a href="javascript:void(0)" class="badge bg-danger"
                                                   onclick="dataDelete({{ $item->id }}, '{{ $pageHeader['base_url'] }}')"><i class="fas fa-trash"></i></a>

                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center">No record found</td></tr>
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
