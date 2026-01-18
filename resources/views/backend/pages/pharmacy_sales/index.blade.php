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
