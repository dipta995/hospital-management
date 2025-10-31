@extends('backend.layouts.master')

@section('title')
    List of {{ $pageHeader['title'] }} Items
@endsection

@push('styles')
    <!-- Add any custom CSS if needed -->
@endpush

@section('admin-content')
<!-- partial -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{ $pageHeader['title'] }} Items List
                            <div class="d-flex justify-content-end mb-3">
                                <div class="dropdown">
                                    <button class="btn btn-outline-primary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter by Expiry
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                                        <li><a class="dropdown-item filter-option" href="#" data-filter="all">Show All</a></li>
                                        <li><a class="dropdown-item filter-option" href="#" data-filter="expired">Expired</a></li>
                                        <li><a class="dropdown-item filter-option" href="#" data-filter="unexpired">Not Expired</a></li>
                                    </ul>
                                </div>
                            </div>
                        </h4>
                        <p class="card-description">
                            @include('backend.layouts.partials.message')
                        </p>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Supplier</th>
                                        <th>Quantity</th>
                                        <th>Left</th>
                                        <th>Expiry Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($datas as $item)
                                        @php
                                            $isExpired = \Carbon\Carbon::parse($item->expiry_date)->isPast();
                                            $expiryDate = \Carbon\Carbon::parse($item->expiry_date)->format('Y-m-d'); // Format the expiry date
                                        @endphp
                                        <tr id="table-data{{ $item->id }}" data-expired="{{ $isExpired ? 'yes' : 'no' }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->item->name ?? 'N/A' }}</td>
                                            <td>{{ $item->supplier->name ?? 'N/A' }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ $item->quantity-$item->quantity_spend }}</td>
                                            <td>
                                                <!-- Show the expiry date instead of "Not Expired" or "Expired" -->
                                                <span class="badge {{ $isExpired ? 'bg-danger' : 'bg-success' }}">
                                                    {{ $expiryDate }}
                                                </span>
                                            </td>

                                            <td>
                                                <a href="{{ route('admin.purchases.edit-item', $item->id) }}" class="badge bg-info" title="Edit Item">
                                                    <i class="fas fa-pen"></i>
                                                </a>

                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">
                                                No records found.
                                                <a href="{{ route($pageHeader['create_route']) }}" class="btn btn-info">Create</a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-end mt-3">
                            {!! $datas->links() !!}
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
    $(document).ready(function () {
        $('.filter-option').on('click', function (e) {
            e.preventDefault();
            var filter = $(this).data('filter');

            $('tbody tr').show(); // Reset all rows

            if (filter === 'expired') {
                $('tbody tr').filter(function () {
                    return $(this).data('expired') !== 'yes';
                }).hide();
            } else if (filter === 'unexpired') {
                $('tbody tr').filter(function () {
                    return $(this).data('expired') !== 'no';
                }).hide();
            }
        });
    });
</script>
@endpush
