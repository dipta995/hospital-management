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
                            <form action="" method="GET" class="mb-4">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="start_date">Start Date:</label>
                                        <input type="date" name="start_date" id="start_date" class="form-control"
                                               value="{{ request('start_date') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="end_date">End Date:</label>
                                        <input type="date" name="end_date" id="end_date" class="form-control"
                                               value="{{ request('end_date') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="end_date">Export (PDF)</label>
                                        <select class="form-control" name="export" id="">
                                            <option value="">No</option>
                                            <option value="pdf">PDF</option>
                                            {{--                                            <option value="csv">CSV</option>--}}
                                        </select>
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <a href="{{ route('admin.costs.index') }}"
                                           class="btn btn-secondary ms-2">Reset</a>
                                    </div>
                                </div>
                            </form>
                            <p>Total: {{ $totalAmount }}</p>
                            <p class="card-description">
                                @include('backend.layouts.partials.message')
                            </p>
                            <div class="table-responsive">

                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Reason</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($datas as $item)
                                        <tr id="table-data{{ $item->id }}">
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $item->reeferBy->name ?? ($item->category->name ?? 'N/A') }}</td>
                                            <td>{{ $item->reason }}</td>
                                            <td>{{ $item->amount }}</td>
                                            <td>{{ $item->creation_date }}</td>

                                            <td>

{{--                                                @if (\Carbon\Carbon::parse($item->created_at)->setTimezone('Asia/Dhaka')->isToday())--}}
                                                    <a href="{{ route($pageHeader['edit_route'], $item->id) }}"
                                                       class="badge bg-info">
                                                        <i class="fas fa-pen"></i>
                                                    </a>
{{--                                                @endif--}}

                                                <a class="badge bg-danger" href="javascript:void(0)"
                                                   onclick="dataDelete({{ $item->id }},'{{ $pageHeader['base_url'] }}')"><i
                                                        class="fas fa-trash"></i></a>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function dataDelete(id, base_url) {
    if (confirm("Are you sure you want to delete this cost?")) {
        $.ajax({
            url: base_url + '/' + id,
            type: 'DELETE',
            data: {_token: '{{ csrf_token() }}'},
            success: function (response) {
                $('#table-data' + id).remove();
                alert('Deleted successfully');

                // ✅ if response contains employee_id, update their after cost
                if (response.employee_id) {
                    updateEmployeeAfterCost(response.employee_id);
                }
            },
            error: function () {
                alert('Error deleting cost');
            }
        });
    }
}

function updateEmployeeAfterCost(employeeId) {
    $.ajax({
        url: '/admin/employees/' + employeeId + '/after-cost',
        type: 'GET',
        success: function (data) {
            // Update the “After Costs” text
            $('#employee-' + employeeId).text(data.after_cost.toFixed(2));
        },
        error: function() {
            console.error('Could not update after cost');
        }
    });
}
</script>



@endpush
