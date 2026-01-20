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
                            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-users"></i> Go to Patient List
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Patient</th>
                                        <th>Total</th>
                                        <th>Discount</th>
                                        <th>Paid</th>
                                        <th>Due</th>
                                        <th>Created Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($datas as $item)
                                        @php
                                            $total = $item->total_amount ?? 0;
                                            $discount = $item->discount_amount ?? 0;
                                            $paid = $item->receptPayments->sum('paid_amount');
                                            $net = $total - $discount;
                                            $due = $net - $paid;
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->user->name ?? '' }}</td>
                                            <td>{{ number_format($total, 2) }}</td>
                                            <td><strong class="text-danger">{{ number_format($discount, 2) }}</strong></td>
                                            <td>{{ number_format($paid, 2) }}</td>
                                            <td class="fw-semibold {{ $due > 0 ? 'text-danger' : 'text-success' }}">
                                                {{ number_format($due, 2) }}
                                            </td>
                                            <td>{{ $item->created_date }}</td>
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
                                        <tr><td colspan="8" class="text-center">No record found</td></tr>
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
