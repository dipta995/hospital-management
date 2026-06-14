@extends('backend.layouts.master')
@section('title')
    {{ $pageHeader['title'] }}
@endsection
@push('styles')
    @include('backend.layouts.partials.report-styles')
@endpush
@section('admin-content')
    @php
        $fmt = fn ($n) => number_format((float) $n, 2);
        $type = request('type', 'diagnostic');
        $periodLabel = request('start_date') || request('end_date')
            ? (request('start_date') ?: '…') . ' → ' . (request('end_date') ?: '…')
            : 'Today (' . now()->format('d M Y') . ')';
        $costCategories = \App\Models\CostCategory::where('branch_id', auth()->user()->branch_id)->get();
    @endphp

    <div class="inv-page container-fluid py-3">
        @include('backend.layouts.partials.report-hero', [
            'reportTitle' => 'Cost Report',
            'reportSubtitle' => 'Expenses paid out — filter on screen or export PDF by category / date',
            'reportIcon' => 'fa-receipt',
            'resetRoute' => route('admin.reports.costs'),
        ])

        @include('backend.layouts.partials.message')

        <div class="inv-kpi-grid">
            <div class="inv-kpi">
                <div class="inv-kpi-icon discount"><i class="fas fa-money-bill-wave"></i></div>
                <div>
                    <div class="inv-kpi-label">Total (filtered)</div>
                    <div class="inv-kpi-value">৳ {{ $fmt($totalAmount ?? 0) }}</div>
                </div>
            </div>
            <div class="inv-kpi">
                <div class="inv-kpi-icon"><i class="fas fa-list"></i></div>
                <div>
                    <div class="inv-kpi-label">Showing</div>
                    <div class="inv-kpi-value">{{ $datas->total() }} records</div>
                </div>
            </div>
            <div class="inv-kpi">
                <div class="inv-kpi-icon"><i class="fas fa-hospital"></i></div>
                <div>
                    <div class="inv-kpi-label">Type</div>
                    <div class="inv-kpi-value" style="font-size:0.95rem">{{ ucfirst($type) }}</div>
                </div>
            </div>
            <div class="inv-kpi">
                <div class="inv-kpi-icon"><i class="fas fa-calendar"></i></div>
                <div>
                    <div class="inv-kpi-label">Period</div>
                    <div class="inv-kpi-value" style="font-size:0.9rem">{{ $periodLabel }}</div>
                </div>
            </div>
        </div>

        <div class="inv-panel mb-3">
            <form action="" method="GET" class="inv-filter-toolbar">
                <div class="filter-field">
                    <label class="form-label">From</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="filter-field">
                    <label class="form-label">To</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="filter-field">
                    <label class="form-label">Type</label>
                    <select class="form-select" name="type">
                        <option value="diagnostic" @selected($type === 'diagnostic')>Diagnostic</option>
                        <option value="hospital" @selected($type === 'hospital')>Hospital</option>
                    </select>
                </div>
                <div class="inv-filter-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Apply</button>
                    <a href="{{ route('admin.costs.index') }}" class="btn btn-outline-secondary btn-sm">Manage costs</a>
                </div>
            </form>

            <div class="p-3 border-bottom">
                <p class="text-muted small mb-3">
                    <i class="fas fa-file-pdf"></i>
                    <strong>PDF exports</strong> use the same date range and type. Choose a category where required, then submit — PDF opens in a new tab.
                </p>
                <div class="row g-3">
                    <div class="col-lg-4">
                        <div class="report-export-card h-100">
                            <h6><i class="fas fa-folder-open me-1"></i> One category</h6>
                            <form action="{{ route('admin.costs.report-category-pdf-id') }}" method="GET" class="row g-2">
                                <div class="col-12">
                                    <label class="form-label small">Category</label>
                                    <select class="form-select form-select-sm" name="cost_category_id">
                                        @foreach($costCategories as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                        <option value="">PC</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small">From</label>
                                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small">To</label>
                                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small">Type</label>
                                    <select class="form-select form-select-sm" name="type">
                                        <option value="diagnostic" @selected($type === 'diagnostic')>Diagnostic</option>
                                        <option value="hospital" @selected($type === 'hospital')>Hospital</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-outline-primary btn-sm w-100"><i class="fas fa-download"></i> Export PDF</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="report-export-card h-100">
                            <h6><i class="fas fa-layer-group me-1"></i> All categories</h6>
                            <form action="{{ route('admin.costs.report-category-pdf') }}" method="GET" class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small">From</label>
                                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small">To</label>
                                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small">Type</label>
                                    <select class="form-select form-select-sm" name="type">
                                        <option value="diagnostic" @selected($type === 'diagnostic')>Diagnostic</option>
                                        <option value="hospital" @selected($type === 'hospital')>Hospital</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-outline-primary btn-sm w-100"><i class="fas fa-download"></i> Export PDF</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="report-export-card h-100">
                            <h6><i class="fas fa-calendar-day me-1"></i> By date (line list)</h6>
                            <form action="{{ route('admin.costs.report-pdf') }}" method="GET" class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small">From</label>
                                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small">To</label>
                                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small">Type</label>
                                    <select class="form-select form-select-sm" name="type">
                                        <option value="diagnostic" @selected($type === 'diagnostic')>Diagnostic</option>
                                        <option value="hospital" @selected($type === 'hospital')>Hospital</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-outline-primary btn-sm w-100"><i class="fas fa-download"></i> Export PDF</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table inv-table mb-0">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Paid to / Category</th>
                        <th>Reason</th>
                        <th class="text-end">Amount</th>
                        <th>Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($datas as $item)
                        <tr>
                            <td>{{ ($datas->currentPage() - 1) * $datas->perPage() + $loop->iteration }}</td>
                            <td>
                                <strong>{{ $item->reeferBy->name ?? ($item->category->name ?? '—') }}</strong>
                                @if($item->category)
                                    <div class="small text-muted">{{ $item->category->name }}</div>
                                @endif
                            </td>
                            <td>{{ $item->reason ?? '—' }}</td>
                            <td class="text-end fw-semibold">৳ {{ $fmt($item->amount) }}</td>
                            <td>{{ $item->creation_date ? \Carbon\Carbon::parse($item->creation_date)->format('d M Y') : '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-5">No cost records for this filter.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end p-3">{!! $datas->appends(request()->query())->links() !!}</div>
        </div>
    </div>
@endsection
