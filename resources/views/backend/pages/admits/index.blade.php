@extends('backend.layouts.master')

@section('title')
    {{ ucfirst($pageHeader['title']) }}
@endsection

@push('styles')
    @include('backend.layouts.partials.invoice-styles')
@endpush

@section('admin-content')
    <div class="inv-page container-fluid py-3">

        <div class="inv-hero">
            <div class="inv-hero-inner">
                <div class="inv-hero-left">
                    <div class="inv-hero-icon"><i class="fas fa-bed"></i></div>
                    <div>
                        <h1 class="inv-hero-title">Patient Admits</h1>
                        <p class="inv-hero-sub">Manage admissions, release and receipts</p>
                    </div>
                </div>
                <div class="inv-hero-actions">
                    <a href="{{ route('admin.admits.index') }}" class="inv-btn-glass">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </a>
                    <a href="{{ route('admin.users.index', ['from' => 'admit']) }}" class="inv-btn-white">
                        <i class="fas fa-plus"></i> Admit New
                    </a>
                </div>
            </div>
        </div>

        @include('backend.layouts.partials.message')

        <div class="inv-panel">
            <div class="inv-panel-head" data-bs-toggle="collapse" data-bs-target="#admitFilterCollapse">
                <h6><i class="fas fa-filter"></i> Filters</h6>
                <i class="fas fa-chevron-down text-muted"></i>
            </div>
            <div class="collapse show" id="admitFilterCollapse">
                <div class="inv-panel-body">
                    <form method="GET" action="{{ route('admin.admits.index') }}" autocomplete="off">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4 position-relative">
                                <label for="admit_search" class="form-label">Patient (Phone / Name)</label>
                                <input type="text" id="admit_search" name="query" class="form-control"
                                       placeholder="Type phone or name" value="{{ request('query') }}">
                                <div id="admit_suggestions" class="list-group position-absolute w-100"
                                     style="z-index: 1050; display:none;"></div>
                                <input type="hidden" id="admit_user_id" name="user_id" value="{{ request('user_id') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Release Status</label>
                                <select name="status" id="status" class="form-select">
                                    @php $currentStatus = request('status', 'all'); @endphp
                                    <option value="not_released" {{ $currentStatus === 'not_released' ? 'selected' : '' }}>Not Released</option>
                                    <option value="released" {{ $currentStatus === 'released' ? 'selected' : '' }}>Released</option>
                                    <option value="all" {{ $currentStatus === 'all' ? 'selected' : '' }}>All</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="date_range" class="form-label">Date Range</label>
                                <select name="date_range" id="date_range" class="form-select">
                                    @php $currentRange = $appliedDateRange ?? request('date_range'); @endphp
                                    <option value="">All Time</option>
                                    <option value="today" {{ $currentRange === 'today' ? 'selected' : '' }}>Today</option>
                                    <option value="this_week" {{ $currentRange === 'this_week' ? 'selected' : '' }}>This Week</option>
                                    <option value="last_week" {{ $currentRange === 'last_week' ? 'selected' : '' }}>Last Week</option>
                                    <option value="last_month" {{ $currentRange === 'last_month' ? 'selected' : '' }}>Last Month</option>
                                    <option value="current_month" {{ $currentRange === 'current_month' ? 'selected' : '' }}>Current Month</option>
                                </select>
                            </div>
                        </div>
                        <div class="inv-filter-actions">
                            <a href="{{ route('admin.admits.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                            <button type="submit" class="inv-btn-filter"><i class="fas fa-search me-1"></i> Apply Filters</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="inv-table-wrap">
            <div class="table-responsive">
                <table class="table inv-table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Referrer</th>
                        <th>Father/Spouse</th>
                        <th>Bed/Cabin</th>
                        <th>Admit Date</th>
                        <th>Release</th>
                        <th class="inv-actions-cell">
                            <div class="inv-actions-head">
                                <span title="Create Receipt"><i class="fas fa-file-invoice-dollar"></i></span>
                                <span title="View Receipts"><i class="fas fa-list"></i></span>
                                <span title="Print"><i class="fas fa-print"></i></span>
                                <span title="Edit"><i class="fas fa-pen"></i></span>
                                <span title="Delete"><i class="fas fa-trash"></i></span>
                            </div>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($datas as $data)
                        @php $isReleased = !empty($data->release_at); @endphp
                        <tr id="table-data{{ $data->id }}">
                            <td data-label="#"><span class="inv-inv-no">{{ $loop->iteration }}</span></td>
                            <td data-label="Patient">
                                <strong class="inv-patient-name">{{ optional($data->user)->name ?? 'Unknown' }}</strong>
                            </td>
                            <td data-label="Doctor">{{ $data->drreefer?->name ?? '—' }}</td>
                            <td data-label="Referrer">{{ $data->reefer?->name ?? '—' }}</td>
                            <td data-label="Father/Spouse">{{ $data->father_or_spouse ?? '—' }}</td>
                            <td data-label="Bed/Cabin">{{ $data->bed_or_cabin ?? '—' }}</td>
                            <td data-label="Admit Date">{{ $data->admit_at ?? '—' }}</td>
                            <td data-label="Release">
                                @if(!$isReleased)
                                    <a href="{{ route('admin.admits.release.details', $data->id) }}"
                                       class="inv-status pending text-decoration-none">
                                        <span class="inv-status-dot"></span> Release
                                    </a>
                                @else
                                    <span class="inv-status complete">
                                        <span class="inv-status-dot"></span> Released
                                    </span>
                                    <div class="inv-patient-id mt-1">{{ $data->release_at }}</div>
                                    <a href="{{ route('admin.admits.release.details', $data->id) }}" target="_blank"
                                       class="small text-primary text-decoration-none">
                                        <i class="fas fa-eye"></i> Preview
                                    </a>
                                @endif
                            </td>
                            <td data-label="Actions" class="inv-actions-cell">
                                <div class="inv-actions-grid">
                                    @if(!$isReleased)
                                        <a href="{{ route('admin.recepts.create').'?admitId='.$data->id.'&for='.$data->user_id }}"
                                           class="inv-act pay" title="Create Receipt">
                                            <i class="fas fa-file-invoice-dollar"></i>
                                        </a>
                                    @else
                                        <span class="inv-act-slot" aria-hidden="true"></span>
                                    @endif

                                    <a href="{{ route('admin.recepts.index').'?for='.$data->id }}"
                                       class="inv-act view" title="View Receipts">
                                        <i class="fas fa-list"></i>
                                    </a>

                                    <a href="{{ route('admin.admits.print', $data->id) }}" target="_blank"
                                       class="inv-act pdf" title="Print">
                                        <i class="fas fa-print"></i>
                                    </a>

                                    @if(!$isReleased)
                                        <a href="{{ route($pageHeader['edit_route'], $data->id) }}"
                                           class="inv-act edit" title="Edit Admit">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <button type="button" class="inv-act del delete-btn" data-id="{{ $data->id }}"
                                                title="Delete Admit">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @else
                                        <span class="inv-act-slot" aria-hidden="true"></span>
                                        <span class="inv-act-slot" aria-hidden="true"></span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="inv-empty">
                                    <i class="fas fa-bed"></i>
                                    <p>No admits found for selected filters.</p>
                                    <a href="{{ route('admin.users.index', ['from' => 'admit']) }}"
                                       class="inv-btn-white" style="color:var(--inv-primary);display:inline-flex;">
                                        <i class="fas fa-plus"></i> Admit New Patient
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
            <span class="text-muted small">Showing {{ $datas->count() }} of {{ $datas->total() }} admits</span>
            {!! $datas->appends(request()->query())->links() !!}
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.jQuery) {
                $('#admit_search').on('input', function () {
                    const query = $(this).val().trim();
                    const $suggestions = $('#admit_suggestions');
                    $('#admit_user_id').val('');

                    if (query.length < 3) {
                        $suggestions.empty().hide();
                        return;
                    }

                    $.ajax({
                        url: '/admin/search-phone',
                        type: 'GET',
                        data: { query: query },
                        success: function (data) {
                            $suggestions.empty();
                            if (!data || !data.length) {
                                $suggestions.hide();
                                return;
                            }
                            data.forEach(function (item) {
                                const label = `${item.name} (${item.phone})`;
                                $('<a href="#" class="list-group-item list-group-item-action"></a>')
                                    .text(label)
                                    .data('user-id', item.userId || item.id)
                                    .data('phone', item.phone)
                                    .data('name', item.name)
                                    .appendTo($suggestions);
                            });
                            $suggestions.show();
                        },
                        error: function () {
                            $suggestions.empty().hide();
                        }
                    });
                });

                $('#admit_suggestions').on('click', '.list-group-item', function (e) {
                    e.preventDefault();
                    $('#admit_search').val(`${$(this).data('name')} (${$(this).data('phone')})`);
                    $('#admit_user_id').val($(this).data('user-id'));
                    $('#admit_suggestions').empty().hide();
                });

                $(document).on('click', function (e) {
                    if (!$(e.target).closest('#admit_search, #admit_suggestions').length) {
                        $('#admit_suggestions').empty().hide();
                    }
                });
            }

            document.querySelectorAll('.delete-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    if (!confirm('Are you sure you want to delete this admit?')) return;
                    const id = this.dataset.id;
                    fetch(`/admin/admits/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 200) {
                                document.getElementById('table-data' + id)?.remove();
                            } else {
                                alert('Failed to delete admit');
                            }
                        });
                });
            });
        });
    </script>
@endpush
