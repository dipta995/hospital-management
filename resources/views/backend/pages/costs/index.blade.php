@extends('backend.layouts.master')
@section('title')
    {{ $pageHeader['title'] }}
@endsection

@push('styles')
    @include('backend.layouts.partials.crud-styles')
    @include('backend.layouts.partials.cost-category-select2-assets')
@endpush

@section('admin-content')
    @php
        $userGuard = Auth::guard('admin')->user();
        $isHospital = Route::is('admin.hospital_costs.index');
        $indexRoute = $isHospital ? 'admin.hospital_costs.index' : 'admin.costs.index';
        $fmt = fn ($n) => number_format((float) $n, 2);
    @endphp

    <div class="crud-page container-fluid py-3">
        @if($isHospital)
            @include('backend.layouts.partials.crud-hero', [
                'heroTitle' => $pageHeader['title'] . ' List',
                'heroSubtitle' => 'Hospital & admit-related expenses',
                'heroIcon' => 'fa-hospital',
                'heroActions' => '<button type="button" class="btn-crud-primary" data-bs-toggle="modal" data-bs-target="#hospitalCostCreateModal"><i class="fas fa-plus"></i> Add Hospital Cost</button>',
            ])
        @else
            @include('backend.layouts.partials.crud-hero', [
                'heroTitle' => $pageHeader['title'] . ' List',
                'heroSubtitle' => 'Diagnostic branch expenses by category',
                'heroIcon' => 'fa-money-bill-wave',
                'heroCreateRoute' => $userGuard->can('costs.create') ? $pageHeader['create_route'] : null,
                'heroCreateLabel' => 'Add Cost',
            ])
        @endif

        <div class="crud-card">
            @include('backend.layouts.partials.message')

            <form action="" method="GET" class="crud-toolbar">
                <div class="row g-2 flex-grow-1">
                    <div class="col-md-3">
                        <label class="form-label" for="start_date">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control"
                               value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="end_date">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control"
                               value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="export">Export</label>
                        <select class="form-select" name="export" id="export">
                            <option value="">No</option>
                            <option value="pdf" @selected(request('export') === 'pdf')>PDF</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route($indexRoute) }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </div>
            </form>

            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                <span class="crud-badge" style="font-size:0.85rem;padding:8px 14px;">
                    <i class="fas fa-calculator me-1"></i> Total: ৳ {{ $fmt($totalAmount) }}
                </span>
            </div>

            <div class="crud-table-wrap">
                <div class="table-responsive">
                    <table class="table crud-table table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Category / Refer</th>
                            <th>Reason</th>
                            <th class="text-end">Amount</th>
                            <th>Date</th>
                            <th class="text-end">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($datas as $item)
                            <tr id="table-data{{ $item->id }}">
                                <td>{{ $loop->iteration + ($datas->currentPage() - 1) * $datas->perPage() }}</td>
                                <td>
                                    <strong>{{ $item->reeferBy->name ?? ($item->category->name ?? 'N/A') }}</strong>
                                    @if($item->category)
                                        <div><small class="text-muted">{{ ucfirst($item->category->type ?? 'diagnostic') }}</small></div>
                                    @endif
                                </td>
                                <td>{{ $item->reason }}</td>
                                <td class="text-end fw-semibold">৳ {{ $fmt($item->amount) }}</td>
                                <td>{{ $item->creation_date ? \Carbon\Carbon::parse($item->creation_date)->format('d M Y') : '—' }}</td>
                                <td class="text-end">
                                    <div class="crud-action-group">
                                        @if($userGuard->can('costs.edit'))
                                            <a href="{{ route($pageHeader['edit_route'], $item->id) }}" class="crud-btn-icon crud-btn-edit" title="Edit">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                        @endif
                                        @if($userGuard->can('costs.delete'))
                                            <a href="javascript:void(0)" class="crud-btn-icon crud-btn-delete" title="Delete"
                                               onclick="costDataDelete({{ $item->id }},'{{ $pageHeader['base_url'] }}')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="crud-empty">
                                    No costs found for this period.
                                    @if(!$isHospital && $userGuard->can('costs.create'))
                                        <a href="{{ route($pageHeader['create_route']) }}" class="btn btn-sm btn-primary ms-2">Create Cost</a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {!! $datas->appends(request()->query())->links() !!}
            </div>
        </div>
    </div>

    @if($isHospital)
        @php
            $hospitalCategories = \App\Models\CostCategory::where('branch_id', auth()->user()->branch_id)
                ->where('type', 'hospital')
                ->orderBy('name')
                ->get();
        @endphp
        <div class="modal fade crud-modal" id="hospitalCostCreateModal" tabindex="-1" aria-labelledby="hospitalCostCreateModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="hospitalCostCreateModalLabel">Add Hospital Cost</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.costs.store') }}">
                        @csrf
                        <div class="modal-body crud-form-grid">
                            <div class="mb-3">
                                <label class="form-label" for="hospital_cost_category_id">Category</label>
                                @if($hospitalCategories->count())
                                    @include('backend.layouts.partials.cost-category-select', [
                                        'categories' => $hospitalCategories,
                                        'id' => 'hospital_cost_category_id',
                                        'name' => 'cost_category_id',
                                        'selected' => old('cost_category_id'),
                                    ])
                                @else
                                    <input type="text" class="form-control" value="No hospital cost categories configured" readonly>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="hospital_cost_reason">Reason</label>
                                <textarea name="reason" id="hospital_cost_reason" class="form-control" rows="3" placeholder="Enter reason">{{ old('reason') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="hospital_cost_amount">Amount</label>
                                <input type="number" step="0.01" min="0.01" name="amount" id="hospital_cost_amount" class="form-control" value="{{ old('amount') }}" required>
                            </div>
                            <div class="mb-0">
                                <label class="form-label" for="hospital_cost_date">Date</label>
                                <input type="date" name="date" id="hospital_cost_date" class="form-control"
                                       value="{{ old('date', \Carbon\Carbon::now('Asia/Dhaka')->format('Y-m-d')) }}" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-crud-submit">Save Hospital Cost</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function initCostCategorySelect() {
            $('.cost-category-select').each(function () {
                if ($(this).hasClass('select2-hidden-accessible')) return;
                $(this).select2({
                    placeholder: $(this).data('placeholder') || 'Search or select category...',
                    allowClear: true,
                    width: '100%',
                    minimumResultsForSearch: 0,
                    dropdownParent: $(this).closest('.modal').length ? $(this).closest('.modal') : $(document.body)
                });
            });
        }

        document.addEventListener('DOMContentLoaded', initCostCategorySelect);

        var hospitalModal = document.getElementById('hospitalCostCreateModal');
        if (hospitalModal) {
            hospitalModal.addEventListener('shown.bs.modal', initCostCategorySelect);
        }

        function costDataDelete(id, baseUrl) {
            if (!confirm('Are you sure you want to delete this cost?')) return;

            $.ajax({
                url: baseUrl + '/' + id,
                type: 'DELETE',
                data: {_token: '{{ csrf_token() }}'},
                success: function (response) {
                    $('#table-data' + id).remove();
                    if (response.employee_id) {
                        updateEmployeeAfterCost(response.employee_id);
                    }
                },
                error: function () {
                    alert('Error deleting cost');
                }
            });
        }

        function updateEmployeeAfterCost(employeeId) {
            $.ajax({
                url: '/admin/employees/' + employeeId + '/after-cost',
                type: 'GET',
                success: function (data) {
                    $('#employee-' + employeeId).text(data.after_cost.toFixed(2));
                }
            });
        }
    </script>
@endpush
