@extends('backend.layouts.master')
@section('title')
    Lab Tests
@endsection
@push('styles')
    @include('backend.layouts.partials.lab-styles')
@endpush
@section('admin-content')
    <div class="lab-page crud-page container-fluid py-3">
        @include('backend.layouts.partials.crud-hero', [
            'heroTitle' => 'Lab Tests',
            'heroSubtitle' => 'Individual test queue with status filters',
            'heroIcon' => 'fa-vial',
        ])

        @include('backend.layouts.partials.message')

        <div class="crud-card">
            <form method="GET" class="crud-toolbar">
                <div class="row g-2 align-items-end w-100">
                    <div class="col-md-2">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Invoice</label>
                        <input type="text" name="invoice_number" class="form-control" value="{{ request('invoice_number') }}"
                               placeholder="Invoice no.">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select class="form-control" name="status">
                            <option value="">All</option>
                            @foreach(\App\Models\InvoiceList::$statusArray as $item)
                                <option @selected($item == request('status')) value="{{ $item }}">{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 d-flex gap-2 flex-wrap">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('admin.labs.tests') }}" class="btn btn-outline-secondary">Reset</a>
                        <a href="{{ route('admin.labs.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-list me-1"></i> Invoice Queue
                        </a>
                    </div>
                </div>
            </form>

            <div class="crud-table-wrap">
                <div class="table-responsive">
                    <table class="table crud-table table-hover mb-0">
                        <thead>
                        <tr>
                            <th>Test</th>
                            <th>Invoice / Patient</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($datas as $item)
                            @php
                                $badgeClass = match ($item->status) {
                                    \App\Models\InvoiceList::$statusArray[0] => 'pending',
                                    \App\Models\InvoiceList::$statusArray[1] => 'processing',
                                    \App\Models\InvoiceList::$statusArray[2] => 'complete',
                                    default => 'rejected',
                                };
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $item->product->name ?? 'N/A' }}</strong>
                                </td>
                                <td>
                                    <div>{{ $item->invoice->invoice_number ?? '—' }}</div>
                                    <div class="small text-muted">
                                        {{ $item->invoice->patient_no ?? '—' }} · {{ $item->invoice->patient_name ?? 'N/A' }}
                                    </div>
                                </td>
                                <td>
                                    <span class="lab-badge {{ $badgeClass }}">{{ $item->status }}</span>
                                    @if(in_array($item->status, [\App\Models\InvoiceList::$statusArray[0], \App\Models\InvoiceList::$statusArray[1]], true))
                                        <div class="form-check form-switch mt-2">
                                            <input onclick="labStatusUpdate({{ $item->id }})"
                                                   {{ $item->status == \App\Models\InvoiceList::$statusArray[1] ? 'checked' : '' }}
                                                   class="form-check-input" type="checkbox"
                                                   id="status_switch{{ $item->id }}"
                                                   title="Toggle processing">
                                            <label class="form-check-label small text-muted" for="status_switch{{ $item->id }}">Toggle</label>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="lab-crud-actions justify-content-end">
                                        @if($item->status == \App\Models\InvoiceList::$statusArray[2])
                                            @if($item->document != null)
                                                <a class="lab-btn download" target="_blank"
                                                   href="{{ route('admin.lab.report.file-download', $item->id) }}" title="Download">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @endif
                                            <a class="lab-btn edit"
                                               href="{{ route('admin.labs.edit', [$item->id]) . '?status=' . request('status') }}" title="Edit report">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                        @elseif($item->status == \App\Models\InvoiceList::$statusArray[1])
                                            <a class="lab-btn edit" href="{{ route('admin.labs.edit', $item->id) }}" title="Edit">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                        @endif
                                        @if($item->invoice_id)
                                            <a class="lab-btn view" href="{{ route('admin.labs.show', $item->invoice_id) }}" title="Open invoice lab">
                                                <i class="fas fa-flask"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="lab-empty">No tests found.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($datas->hasPages())
                <div class="d-flex justify-content-end p-3 border-top">
                    {!! $datas->appends(request()->query())->links() !!}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function labStatusUpdate(id) {
        const Toast = Swal.mixin({
            toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true,
        });
        $.ajax({
            url: '/admin/labs/status/' + id,
            type: 'GET',
            data: { _token: $('meta[name="csrf-token"]').attr('content') },
            success: function () {
                Toast.fire({ icon: 'success', title: 'Status updated' });
                location.reload();
            },
            error: function () {
                Toast.fire({ icon: 'error', title: 'Could not update status' });
            },
        });
    }
</script>
@endpush
