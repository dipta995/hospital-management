@extends('backend.layouts.master')

@section('title')
    List of {{ $pageHeader['title'] }}'s
@endsection

@push('styles')
<!-- Add any styles you need here -->
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
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Supplier</th>
                                        <th>Purchase Date</th>
                                        <th>Total Cost</th>
                                        <th>Paid Amount</th>
                                        <th>Due Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($datas as $item)
                                        <tr id="table-data{{ $item->id }}">
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $item->supplier->name }}</td>
                                            <td>{{ $item->purchase_date }}</td>
                                            <td>{{ $item->total_cost }}</td>
                                            <td>{{ $item->purchase_paid_sum_amount  }}</td>
                                            <td>{{ $item->total_cost-$item->purchase_paid_sum_amount  }}</td>
                                            <td>
                                                <a href="{{ route('admin.purchases.show', $item->id) }}"
                                                   class="badge bg-success"><i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route($pageHeader['edit_route'], $item->id) }}"
                                                   class="badge bg-info"><i class="fas fa-pen"></i>
                                                </a>
                                                <a class="badge bg-danger" href="javascript:void(0)"
                                                   onclick="dataDelete({{ $item->id }}, '{{ $pageHeader['base_url'] }}')"><i
                                                        class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7">No record Found
                                                <a href="{{ route($pageHeader['create_route']) }}"
                                                   class="btn btn-info">Create</a>
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
<!-- main-panel ends -->
@endsection

@push('scripts')
<script>
    function dataDelete(id, base_url) {
        // Confirm delete action
        if (confirm("Are you sure you want to delete this purchase?")) {
            // Use a form submission to send the DELETE request
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = base_url + '/' + id;
            form.innerHTML = '@csrf @method("DELETE")'; // CSRF protection and DELETE method

            // Append the form to the body and submit it
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush
