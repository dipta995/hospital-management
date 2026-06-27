@extends('backend.layouts.master')
@section('title')
    Lab Queue
@endsection
@push('styles')
    @include('backend.layouts.partials.lab-styles')
@endpush
@section('admin-content')
    @php
        $stats = $labStats ?? [];
        $todayDefault = !request()->filled('start_date') && !request()->filled('end_date');
    @endphp
    <div class="lab-page crud-page container-fluid py-3">
        @include('backend.layouts.partials.crud-hero', [
            'heroTitle' => 'Lab Queue',
            'heroSubtitle' => ($todayDefault ? "Today's invoices" : 'Filtered invoices') . ' · Process tests and upload reports',
            'heroIcon' => 'fa-flask',
        ])

        @include('backend.layouts.partials.message')

        <div class="lab-kpi-grid">
            <div class="lab-kpi">
                <div class="lab-kpi-label">Invoices</div>
                <div class="lab-kpi-value">{{ $stats['invoices'] ?? 0 }}</div>
                <div class="lab-kpi-sub">In current filter</div>
            </div>
            <div class="lab-kpi">
                <div class="lab-kpi-label">Pending Queue</div>
                <div class="lab-kpi-value">{{ $stats['pending_invoices'] ?? 0 }}</div>
                <div class="lab-kpi-sub">Invoices with open tests</div>
            </div>
            <div class="lab-kpi">
                <div class="lab-kpi-label">Completed</div>
                <div class="lab-kpi-value">{{ $stats['complete_invoices'] ?? 0 }}</div>
                <div class="lab-kpi-sub">All tests done</div>
            </div>
            <div class="lab-kpi">
                <div class="lab-kpi-label">Tests</div>
                <div class="lab-kpi-value">{{ ($stats['tests_pending'] ?? 0) + ($stats['tests_processing'] ?? 0) }}</div>
                <div class="lab-kpi-sub">
                    {{ $stats['tests_pending'] ?? 0 }} pending ·
                    {{ $stats['tests_processing'] ?? 0 }} processing ·
                    {{ $stats['tests_complete'] ?? 0 }} done
                </div>
            </div>
        </div>

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
                    <div class="col-md-3">
                        <label class="form-label">Invoice Number</label>
                        <input type="text" name="invoice_number" class="form-control" value="{{ request('invoice_number') }}"
                               placeholder="Search invoice...">
                    </div>
                    <div class="col-md-5 d-flex gap-2 flex-wrap">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i> Filter</button>
                        <a href="{{ route('admin.labs.index') }}" class="btn btn-outline-secondary">Reset</a>
                        <a href="{{ route('admin.labs.tests') }}" class="btn btn-outline-primary">
                            <i class="fas fa-vial me-1"></i> Test Queue
                        </a>
                    </div>
                </div>
            </form>

            <div class="lab-status-guide px-1">
                <span class="guide-label">Test status:</span>
                <span class="lab-badge pending">Pending</span>
                <span class="lab-badge processing">Processing</span>
                <span class="lab-badge complete">Complete</span>
                <span class="guide-label ms-2">Invoice lab:</span>
                <span class="lab-badge invoice-pending">Open</span>
                <span class="lab-badge invoice-complete">Complete</span>
            </div>

            <div class="crud-table-wrap">
                <div class="table-responsive">
                    <table class="table crud-table table-hover mb-0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Patient / Invoice</th>
                            <th>Tests</th>
                            <th>Lab Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($datas as $item)
                            @php
                                $labComplete = $item->isFullyProcessed();
                            @endphp
                            <tr id="table-data{{ $item->id }}">
                                <td>{{ $datas->firstItem() + $loop->index }}</td>
                                <td class="lab-patient-cell">
                                    <strong>{{ $item->patient_name ?? 'N/A' }}</strong>
                                    <div class="lab-patient-meta">
                                        Invoice {{ $item->invoice_number }} · ID {{ $item->patient_no }}
                                    </div>
                                </td>
                                <td>
                                    <div class="lab-test-tags">
                                        @foreach($item->tests as $product)
                                            @php
                                                $badgeClass = match ($product->status) {
                                                    \App\Models\InvoiceList::$statusArray[0] => 'pending',
                                                    \App\Models\InvoiceList::$statusArray[1] => 'processing',
                                                    \App\Models\InvoiceList::$statusArray[2] => 'complete',
                                                    default => 'rejected',
                                                };
                                            @endphp
                                            <span class="lab-badge {{ $badgeClass }}" title="{{ $product->status }}">
                                                {{ $product->product->name ?? 'Test' }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td>
                                    <span class="lab-badge {{ $labComplete ? 'invoice-complete' : 'invoice-pending' }}">
                                        {{ $labComplete ? 'Complete' : 'Open' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="lab-crud-actions justify-content-end">
                                        <a target="_blank" href="{{ route('admin.invoices.pdf-preview', $item->id) }}"
                                           class="lab-btn pdf" title="Invoice PDF"><i class="fas fa-file-pdf"></i></a>
                                        <a href="{{ route('admin.labs.show', $item->id) }}"
                                           class="lab-btn view" title="Open Lab Work"><i class="fas fa-flask"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="lab-empty">
                                    <i class="fas fa-vial d-block mb-2 fs-4"></i>
                                    No lab invoices found for this filter.
                                </td>
                            </tr>
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
