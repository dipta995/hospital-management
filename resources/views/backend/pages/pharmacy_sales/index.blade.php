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

                            @include('backend.layouts.partials.message')

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
                                        <tr>
                                            <td>{{ $sale->id }}</td>
                                            <td>{{ $sale->sale_date }}</td>
                                            <td>{{ optional($sale->customer)->name }}</td>
                                            <td>{{ optional($sale->customer)->phone }}</td>
                                            <td>{{ $sale->total_amount }}</td>
                                            <td>{{ $sale->paid_amount }}</td>
                                            <td>{{ $sale->due_amount }}</td>
                                            <td>
                                                <a href="{{ route('admin.pharmacy_sales.edit', $sale->id) }}" class="btn btn-sm btn-info">Edit</a>
                                                <a href="{{ route('admin.pharmacy_sales.pdf-preview', $sale->id) }}" class="btn btn-sm btn-success" target="_blank">Invoice</a>
                                                @if($sale->due_amount > 0)
                                                    <form action="{{ route('admin.pharmacy_sales.due-pay', $sale->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="number" name="paid_amount" step="0.01" min="0.01" max="{{ $sale->due_amount }}" class="form-control form-control-sm d-inline-block w-auto" placeholder="Pay" required>
                                                        <button type="submit" class="btn btn-sm btn-primary">Pay</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No data found.</td>
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
@extends('backend.layouts.master')

@section('title')
    List of {{ $pageHeader['title'] }}'s
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
                        <h4 class="card-title">{{ $pageHeader['title'] }}'s List</h4>
                        <p class="card-description">
                            @include('backend.layouts.partials.message')
                        </p>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Sale Date</th>
                                    <th>Total</th>
                                    <th>Discount</th>
                                    <th>Paid</th>
                                    <th>Due</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($datas as $item)
                                    <tr id="table-data{{ $item->id }}">
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ optional($item->customer)->name }}</td>
                                        <td>{{ $item->sale_date }}</td>
                                        <td>{{ $item->total_amount }}</td>
                                        <td>{{ $item->discount_amount }}</td>
                                        <td>{{ $item->paid_amount }}</td>
                                        <td>{{ $item->due_amount }}</td>
                                        <td>
                                            <a href="{{ route($pageHeader['edit_route'], $item->id) }}" class="badge bg-info"><i class="fas fa-pen"></i></a>
                                            <a class="badge bg-danger" href="javascript:void(0)" onclick="dataDelete({{ $item->id }}, '{{ $pageHeader['base_url'] }}')"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8">No record Found
                                            <a href="{{ route($pageHeader['create_route']) }}" class="btn btn-info">Create</a>
                                        </td>
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
