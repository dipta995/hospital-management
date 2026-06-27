@extends('backend.layouts.master')
@section('title')
    Upcoming Tests
@endsection
@push('styles')
    @include('backend.layouts.partials.report-styles')
@endpush
@section('admin-content')
    <div class="report-page container-fluid py-3">
        @include('backend.layouts.partials.report-hero', [
            'reportTitle' => t('menu.upcoming_tests'),
            'reportSubtitle' => t('menu.upcoming_tests_sub'),
            'reportIcon' => 'fa-calendar-check',
        ])

        @include('backend.layouts.partials.message')

        <div class="crud-card">
            <form method="GET" action="{{ route('admin.reports.upcoming-tests') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date', $filterStart ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date', $filterEnd ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ t('menu.invoice_list') }}</label>
                        <input type="text" name="invoice_number" class="form-control" value="{{ request('invoice_number') }}" placeholder="Invoice #">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">{{ t('common.filter') }}</button>
                        <a href="{{ route('admin.reports.upcoming-tests') }}" class="btn btn-outline-secondary">{{ t('common.reset_filters') }}</a>
                    </div>
                </div>
            </form>
        </div>

        @if(!empty($dateRangeLabel))
            <div class="report-range-badge"><i class="fas fa-calendar-alt"></i> {{ $dateRangeLabel }}</div>
        @endif

        <div class="crud-card mt-3">
            <div class="crud-table-wrap">
                <div class="table-responsive">
                    <table class="table crud-table report-table mb-0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Invoice</th>
                            <th>Patient</th>
                            <th>Phone</th>
                            <th>Follow-up</th>
                            <th>Test</th>
                            <th>Doctor</th>
                            <th>Note</th>
                            <th class="text-end">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($datas as $item)
                            <tr>
                                <td>{{ $datas->firstItem() + $loop->index }}</td>
                                <td class="nowrap">
                                    {{ optional($item->invoice)->invoice_number ?? 'N/A' }}
                                    <div class="small text-muted">#{{ optional($item->invoice)->patient_no ?? '—' }}</div>
                                </td>
                                <td>{{ optional($item->invoice)->patient_name ?? 'N/A' }}</td>
                                <td>{{ optional($item->invoice)->patient_phone ?? '—' }}</td>
                                <td class="nowrap fw-semibold">
                                    {{ $item->followup_date ? \Carbon\Carbon::parse($item->followup_date)->format('d M Y') : 'N/A' }}
                                </td>
                                <td>
                                    <span class="crud-badge {{ $item->status === \App\Models\InvoiceList::$statusArray[0] ? 'bg-danger text-white' : 'bg-warning text-dark' }}">
                                        {{ optional($item->product)->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>{{ optional(optional($item->invoice)->reeferDr)->name ?? '—' }}</td>
                                <td class="note-cell">{{ $item->note ?? '—' }}</td>
                                <td class="text-end">
                                    @if($item->invoice_id)
                                        <a href="{{ route('admin.labs.show', $item->invoice_id) }}" class="btn btn-sm btn-outline-primary">Lab</a>
                                    @endif
                                    <form method="POST" action="{{ route('admin.lab.followup.update', $item->id) }}" class="js-clear-followup-form d-inline">
                                        @csrf
                                        <input type="hidden" name="note" value="">
                                        <input type="hidden" name="followup_date" value="">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Clear</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="crud-empty">No upcoming tests found.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="d-flex justify-content-end mt-3">{{ $datas->links() }}</div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.js-clear-followup-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            Swal.fire({
                toast: true, position: 'top-end', icon: 'warning',
                title: 'Clear note and follow-up date?',
                showCancelButton: true, confirmButtonText: 'Yes, clear', cancelButtonText: 'Cancel',
            }).then(function (result) { if (result.isConfirmed) form.submit(); });
        });
    });
</script>
@endpush
