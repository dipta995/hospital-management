@extends('backend.layouts.master')

@section('title')
    List of {{ $pageHeader['title'] }}
@endsection

@push('styles')
@endpush

@section('admin-content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{ $pageHeader['title'] }} List</h4>

                        <form action="" method="GET" class="mb-4">
                            <div class="row">
                                <div class="col-md-2">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                                </div>
                                <div class="col-md-2">
                                    <label for="end_date">End Date</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                                </div>
                                <div class="col-md-2">
                                    <label for="sale_id">Sale ID</label>
                                    <input type="text" name="sale_id" id="sale_id" class="form-control" value="{{ request('sale_id') }}" placeholder="Sale ID">
                                </div>
                                <div class="col-md-3">
                                    <label for="phone">Phone</label>
                                    <input type="text" name="phone" id="phone" class="form-control" value="{{ request('phone') }}" placeholder="Customer phone">
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('admin.pharmacy_sales.index') }}" class="btn btn-secondary ms-2">Reset</a>
                                </div>
                            </div>
                        </form>

                        <p class="card-description">
                            @include('backend.layouts.partials.message')
                        </p>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Phone</th>
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Due</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($datas as $sale)
                                    <tr id="table-data{{ $sale->id }}">
                                        <td>{{ $sale->id }}</td>
                                        <td>{{ $sale->sale_date }}</td>
                                        <td>{{ optional($sale->customer)->name }}</td>
                                        <td>{{ optional($sale->customer)->phone }}</td>
                                        <td>{{ $sale->total_amount }}</td>
                                        <td>{{ $sale->paid_amount }}</td>
                                        <td>{{ $sale->due_amount }}</td>
                                        <td>
                                            <a href="{{ route($pageHeader['edit_route'], $sale->id) }}" class="badge bg-info"><i class="fas fa-pen"></i></a>
                                            <a href="{{ route('admin.pharmacy_sales.pdf-preview', $sale->id) }}" class="badge bg-success" target="_blank"><i class="fas fa-file-invoice"></i></a>
                                            @if($sale->due_amount > 0)
                                                <form action="{{ route('admin.pharmacy_sales.due-pay', $sale->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="number" name="paid_amount" step="0.01" min="0.01" max="{{ $sale->due_amount }}" class="form-control form-control-sm d-inline-block w-auto" placeholder="Pay" required>
                                                    <button type="submit" class="btn btn-sm btn-primary">Pay</button>
                                                </form>
                                            @endif
                                            <a class="badge bg-danger" href="javascript:void(0)" onclick="dataDelete({{ $sale->id }}, '{{ $pageHeader['base_url'] }}')"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No record found.
                                            <a href="{{ route($pageHeader['create_route']) }}" class="btn btn-info ms-2">Create</a></td>
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
@endsection

@push('scripts')
<script>
    function dataDelete(id, base_url) {
        if (confirm('Are you sure you want to delete this sale?')) {
            let form = document.createElement('form');
            form.method = 'POST';
            form.action = base_url + '/' + id;
            form.innerHTML = '@csrf @method("DELETE")';
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush
