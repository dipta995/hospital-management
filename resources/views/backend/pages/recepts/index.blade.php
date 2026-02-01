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

                        {{-- Filter + Summary Row --}}
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <form method="GET" action="{{ route($pageHeader['index_route']) }}" class="row g-2 align-items-end">
                                    <div class="col-md-4">
                                        <label for="start_date" class="form-label">From Date</label>
                                        <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="end_date" class="form-label">To Date</label>
                                        <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" class="form-control">
                                    </div>
                                    @if(request('for'))
                                        <input type="hidden" name="for" value="{{ request('for') }}">
                                    @endif
                                    <div class="col-md-4 d-flex gap-2">
                                        <button type="submit" class="btn btn-sm btn-primary mt-4">Filter</button>
                                        <a href="{{ route($pageHeader['index_route']) }}" class="btn btn-sm btn-secondary mt-4">Reset</a>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="border rounded p-2 bg-light">
                                    <div><strong>Total Amount:</strong> {{ number_format($total_amount ?? 0, 2) }}</div>
                                    <div><strong>Total Discount:</strong> {{ number_format($total_discount ?? 0, 2) }}</div>
                                    <div><strong>Total Collection:</strong> {{ number_format($total_paid ?? 0, 2) }}</div>
                                </div>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-primary mt-2">
                                    <i class="fas fa-users"></i> Go to Patient List
                                </a>
                            </div>
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
                                            $balance = optional($item->user->customerBalance)->balance ?? 0;
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
                                                @if($due > 0)
                                                    <a href="javascript:void(0)" class="badge bg-success mt-1"
                                                       data-bs-toggle="modal"
                                                       data-bs-target="#receptPaymentModal"
                                                       data-id="{{ $item->id }}"
                                                       data-due="{{ $due }}"
                                                       data-balance="{{ $balance }}">
                                                        <i class="fas fa-money-bill"></i> Pay
                                                    </a>
                                                @endif
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
@include('backend.pages.recepts.partials.payment-modal')
@endsection
