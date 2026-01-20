@extends('backend.layouts.master')
@section('title')
    Create New {{ $pageHeader['title'] }}
@endsection
@push('styles')

@endpush
@section('admin-content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Pharmacy Products</h4>
            @if(Auth::guard('admin')->user()->can('pharmacy_products.create'))
                <a href="{{ route('admin.pharmacy_products.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> {{ __('language.create') }}
                </a>
            @endif
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.pharmacy_products.index') }}" class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                           placeholder="Search by name, generic or barcode">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">Search</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Generic</th>
                        <th>Category</th>
                        <th>Brand</th>
                        <th>Type</th>
                        <th>Unit</th>
                        <th>Purchase Price</th>
                        <th>Sell Price</th>
                        <th>Alert Qty</th>
                        <th class="text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($datas as $key => $data)
                        <tr>
                            <td>{{ $datas->firstItem() + $key }}</td>
                            <td>{{ $data->name }}</td>
                            <td>{{ $data->generic_name }}</td>
                            <td>{{ optional($data->category)->name }}</td>
                            <td>{{ optional($data->brand)->name }}</td>
                            <td>{{ optional($data->type)->name }}</td>
                            <td>{{ optional($data->quantityType)->name }}</td>
                            <td>{{ number_format($data->purchase_price, 2) }}</td>
                            <td>{{ number_format($data->sell_price, 2) }}</td>
                            <td>{{ $data->alert_qty }}</td>
                            <td class="text-center">
                                @if(Auth::guard('admin')->user()->can('pharmacy_products.edit'))
                                    <a href="{{ route('admin.pharmacy_products.edit', $data->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                                @if(Auth::guard('admin')->user()->can('pharmacy_products.delete'))
                                    <button type="button" class="btn btn-sm btn-danger delete-item" data-id="{{ $data->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center">No data found</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $datas->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).on('click', '.delete-item', function () {
            if (!confirm('Are you sure to delete this item?')) return;
            let id = $(this).data('id');
            let url = '{{ route('admin.pharmacy_products.destroy', ':id') }}'.replace(':id', id);

            $.ajax({
                url: url,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (res) {
                    if (res.status === 200) {
                        window.location.reload();
                    } else {
                        alert('Failed to delete.');
                    }
                },
                error: function () {
                    alert('Something went wrong.');
                }
            });
        });
    </script>
@endpush
