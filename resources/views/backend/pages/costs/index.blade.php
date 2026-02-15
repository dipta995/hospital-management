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
                            @if(Route::is('admin.hospital_costs.index'))
                                <div class="mb-3 text-end">
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#hospitalCostCreateModal">
                                        + Add Hospital Cost
                                    </button>
                                </div>
                            @endif
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

@if(Route::is('admin.hospital_costs.index'))
    <!-- Hospital Cost Create Modal -->
    <div class="modal fade" id="hospitalCostCreateModal" tabindex="-1" aria-labelledby="hospitalCostCreateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="hospitalCostCreateModalLabel">Add Hospital Cost</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('admin.costs.store') }}">
                    @csrf
                    <div class="modal-body">
                        @php
                            $hospitalCostCategoryId = \App\Models\Setting::get('admit_hospital_cost_category');
                            $hospitalCategory = $hospitalCostCategoryId
                                ? \App\Models\CostCategory::where('branch_id', auth()->user()->branch_id)
                                    ->where('id', $hospitalCostCategoryId)
                                    ->first()
                                : null;
                        @endphp
                        <div class="form-group mb-2">
                            <label>Category</label>
                            @if($hospitalCategory)
                                <input type="hidden" name="cost_category_id" value="{{ $hospitalCategory->id }}">
                                <input type="text" class="form-control" value="{{ $hospitalCategory->name }}" readonly>
                            @else
                                <input type="text" class="form-control" value="Hospital cost category not configured" readonly>
                            @endif
                        </div>
                        <div class="form-group mb-2">
                            <label for="hospital_cost_reason">Reason</label>
                            <textarea name="reason" id="hospital_cost_reason" class="form-control" rows="3" placeholder="Enter reason"></textarea>
                        </div>
                        <div class="form-group mb-2">
                            <label for="hospital_cost_amount">Amount</label>
                            <input type="number" step="0.01" min="0.01" name="amount" id="hospital_cost_amount" class="form-control" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="hospital_cost_date">Date</label>
                            <input type="date" name="date" id="hospital_cost_date" class="form-control" value="{{ \Carbon\Carbon::now('Asia/Dhaka')->format('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Save Hospital Cost</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
