@php
    use Illuminate\Support\Str;

    $statusColors = [
        \App\Models\InvoiceList::$statusArray[0] => 'pending',
        \App\Models\InvoiceList::$statusArray[1] => 'processing',
        \App\Models\InvoiceList::$statusArray[2] => 'complete',
        \App\Models\InvoiceList::$statusArray[3] => 'rejected',
    ];
    $tests = $singleData->invoiceList ?? collect();
    $completedCount = $tests->where('status', \App\Models\InvoiceList::$statusArray[2])->count();
    $processingCount = $tests->where('status', \App\Models\InvoiceList::$statusArray[1])->count();
    $pendingCount = $tests->where('status', \App\Models\InvoiceList::$statusArray[0])->count();
    $invoiceFollowupDate = $singleData->delivery_at;
    if ($invoiceFollowupDate && strlen((string) $invoiceFollowupDate) >= 10) {
        try {
            $invoiceFollowupDate = \Carbon\Carbon::parse($invoiceFollowupDate)->format('Y-m-d');
        } catch (\Throwable $e) {
            $invoiceFollowupDate = null;
        }
    }
@endphp

@extends('backend.layouts.master')
@section('title')
    Lab · Invoice {{ $singleData->invoice_number }}
@endsection
@push('styles')
    @include('backend.layouts.partials.lab-styles')
    <style>
        .lab-followup-panel {
            background: #fff;
            border: 1px solid #dbeafe;
            border-radius: 16px;
            padding: 18px 20px;
            margin-bottom: 18px;
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.06);
        }
        .lab-followup-panel h6 { font-weight: 800; color: #1e3a8a; margin-bottom: 12px; }
        .lab-followup-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 999px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            font-size: 0.78rem;
            font-weight: 700;
            color: #1d4ed8;
            margin: 0 6px 6px 0;
        }
        .lab-note-btn.has-followup {
            border-color: #93c5fd;
            background: #eff6ff;
            color: #1d4ed8;
        }
        .lab-quick-dates { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; }
        .lab-quick-dates .btn { font-size: 0.72rem; padding: 4px 10px; }
        .lab-schema-alert {
            border-radius: 12px;
            border: 1px solid #fde68a;
            background: #fffbeb;
            padding: 12px 16px;
            margin-bottom: 16px;
            font-size: 0.88rem;
        }
    </style>
@endpush
@section('admin-content')
<div class="crud-page lab-page container-fluid py-3">
    @include('backend.layouts.partials.crud-form-hero', [
        'formTitle' => 'Lab Work · Invoice #' . $singleData->invoice_number,
        'formSubtitle' => $singleData->patient_name . ' · Patient ID ' . $singleData->patient_no,
        'formIcon' => 'fa-flask',
        'formBackRoute' => 'admin.labs.index',
        'formBackLabel' => 'Back to Lab Queue',
    ])

    @include('backend.layouts.partials.message')

    @if(empty($followupSchemaReady))
        <div class="lab-schema-alert">
            <i class="fas fa-database text-warning me-1"></i>
            <strong>Follow-up notes need a database update.</strong>
            Super Admin: Sidebar → <strong>System Updates</strong> → <strong>Lab Follow-up Notes</strong> → Apply.
            Until then, per-test notes cannot be saved.
        </div>
    @endif

    <div class="lab-followup-panel">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
            <h6 class="mb-0"><i class="fas fa-calendar-check me-1"></i> Invoice Follow-up</h6>
            <a href="{{ route('admin.reports.upcoming-tests') }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-list me-1"></i> Upcoming Tests
            </a>
        </div>
        <form method="POST" action="{{ route('admin.invoices.followup.update') }}">
            @csrf
            <input type="hidden" name="invoice_id" value="{{ $singleData->id }}">
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label small fw-semibold">Invoice note</label>
                    <textarea name="note" class="form-control" rows="2" placeholder="General follow-up note for this invoice...">{{ $singleData->note }}</textarea>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Next follow-up date</label>
                    <input type="date" name="followup_date" id="invoice_followup_date" class="form-control" value="{{ $invoiceFollowupDate }}">
                    <div class="lab-quick-dates" data-target="invoice_followup_date">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-days="3">+3 days</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-days="7">+7 days</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-days="14">+14 days</button>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save Invoice Follow-up
                    </button>
                </div>
            </div>
        </form>
        @if(($upcomingFollowups ?? collect())->isNotEmpty())
            <div class="mt-3 pt-3 border-top">
                <div class="small text-muted mb-2">Scheduled test follow-ups on this invoice:</div>
                @foreach($upcomingFollowups as $fu)
                    <span class="lab-followup-chip">
                        <i class="fas fa-vial"></i>
                        {{ $fu->product->name ?? 'Test' }}
                        · {{ \Carbon\Carbon::parse($fu->followup_date)->format('d M Y') }}
                    </span>
                @endforeach
            </div>
        @endif
    </div>

    <div class="lab-kpi-grid">
        <div class="lab-kpi">
            <div class="lab-kpi-label">Invoice</div>
            <div class="lab-kpi-value" style="font-size:1.05rem;">{{ $singleData->invoice_number }}</div>
        </div>
        <div class="lab-kpi">
            <div class="lab-kpi-label">Patient ID</div>
            <div class="lab-kpi-value" style="font-size:1.05rem;">{{ $singleData->patient_no }}</div>
        </div>
        <div class="lab-kpi">
            <div class="lab-kpi-label">Patient Name</div>
            <div class="lab-kpi-value" style="font-size:1.05rem;">{{ $singleData->patient_name }}</div>
        </div>
        <div class="lab-kpi">
            <div class="lab-kpi-label">Phone</div>
            <div class="lab-kpi-value" style="font-size:1.05rem;">{{ $singleData->patient_phone ?: '—' }}</div>
        </div>
    </div>

    <div class="lab-panel">
        <div class="lab-panel-head">
            <span><i class="fas fa-vial me-1"></i> Tests ({{ $tests->count() }})</span>
            <div class="lab-summary-pills">
                <span class="lab-summary-pill">Pending: {{ $pendingCount }}</span>
                <span class="lab-summary-pill">Processing: {{ $processingCount }}</span>
                <span class="lab-summary-pill">Complete: {{ $completedCount }}</span>
            </div>
            <a href="{{ route('admin.test_reports.create', ['invoiceId' => $singleData->id]) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Add Test Report
            </a>
        </div>
        <div class="lab-panel-body">
            <div class="table-responsive">
                <table class="table lab-tests-table">
                    <thead>
                    <tr>
                        <th>Test</th>
                        <th>Status & Actions</th>
                        <th>Reagents</th>
                        <th style="min-width:200px;">Note & Follow-up</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($tests as $item)
                        @php
                            $badgeClass = $statusColors[$item->status] ?? 'rejected';
                            $canAddReagent = in_array($item->status, [
                                \App\Models\InvoiceList::$statusArray[1],
                                \App\Models\InvoiceList::$statusArray[2],
                            ], true);
                            $hasFollowup = !empty($item->note) || !empty($item->followup_date);
                        @endphp
                        <tr>
                            <td data-label="Test">
                                <div class="lab-test-name">{{ $item->product->name ?? 'N/A' }}</div>
                                <div class="lab-test-price">৳ {{ number_format((float) $item->price, 2) }}</div>
                            </td>
                            <td data-label="Status">
                                <span class="lab-badge {{ $badgeClass }}">{{ $item->status }}</span>

                                @if($item->status === \App\Models\InvoiceList::$statusArray[0])
                                    <div class="form-check form-switch mt-2">
                                        <input onclick="labStatusUpdate({{ $item->id }})"
                                               class="form-check-input" type="checkbox"
                                               id="status_switch{{ $item->id }}"
                                               title="Mark as Processing">
                                        <label class="form-check-label small text-muted" for="status_switch{{ $item->id }}">Start processing</label>
                                    </div>
                                @endif

                                @if($item->status === \App\Models\InvoiceList::$statusArray[1])
                                    <div class="form-check form-switch mt-2">
                                        <input onclick="labStatusUpdate({{ $item->id }})"
                                               class="form-check-input" type="checkbox" checked
                                               id="status_switch{{ $item->id }}"
                                               title="Mark as Complete">
                                        <label class="form-check-label small text-muted" for="status_switch{{ $item->id }}">Mark complete</label>
                                    </div>
                                @endif

                                @if($item->status === \App\Models\InvoiceList::$statusArray[2])
                                    <div class="lab-action-links">
                                        @if($item->document != null)
                                            <a class="btn btn-outline-success btn-sm" target="_blank"
                                               href="{{ route('admin.lab.report.file-download', $item->id) }}">
                                                <i class="fas fa-file-download"></i> File
                                            </a>
                                        @endif
                                        @if($item->test_report != null)
                                            <a class="btn btn-outline-primary btn-sm" target="_blank"
                                               href="{{ route('admin.lab.report.pdf-preview', $item->id) }}">
                                                <i class="fas fa-file-pdf"></i> Report PDF
                                            </a>
                                        @endif
                                        <a class="btn btn-outline-secondary btn-sm"
                                           href="{{ route('admin.labs.edit', $item->id) }}">
                                            <i class="fas fa-pen"></i> Edit Report
                                        </a>
                                    </div>
                                @endif
                            </td>
                            <td data-label="Reagents">
                                @if($canAddReagent)
                                    <div class="lab-reagent-box">
                                        <form method="post" action="{{ route('admin.lab.update-item', $item->id) }}">
                                            @csrf
                                            <label class="form-label">Add reagent</label>
                                            <select class="form-control lab-reagent-select" name="items[]" multiple
                                                    id="select2{{ $item->id }}"
                                                    data-placeholder="Search reagent (stock shown)">
                                                @foreach($purchaseItems as $pItem)
                                                    @php($remaining = max((int) $pItem->quantity - (int) $pItem->quantity_spend, 0))
                                                    <option value="{{ $pItem->id }}">
                                                        {{ $pItem->item->name ?? 'Item' }} (Stock: {{ $remaining }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button class="btn btn-sm btn-primary mt-2" type="submit">
                                                <i class="fas fa-plus"></i> Add Selected
                                            </button>
                                        </form>
                                        <div class="lab-reagent-tags">
                                            {!! reAgents($item->id) ?: '<span class="small text-muted">No reagents used yet</span>' !!}
                                        </div>
                                    </div>
                                @else
                                    <span class="small text-muted">Available when processing starts</span>
                                @endif
                            </td>
                            <td data-label="Note">
                                <button type="button"
                                        class="btn btn-sm btn-outline-secondary lab-note-btn {{ $hasFollowup ? 'has-followup' : '' }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#invoiceItemFollowupModal{{ $item->id }}"
                                        @if(empty($followupSchemaReady)) disabled title="Install Lab Follow-up schema first" @endif>
                                    <i class="fas fa-sticky-note"></i>
                                    {{ $hasFollowup ? 'Edit Note' : 'Add Note' }}
                                </button>
                                @if($hasFollowup)
                                    <div class="lab-note-preview">
                                        @if($item->note)
                                            <strong>Note:</strong> {{ Str::limit($item->note, 100) }}<br>
                                        @endif
                                        @if($item->followup_date)
                                            <strong>Next follow-up:</strong>
                                            <span class="text-primary fw-semibold">
                                                {{ \Carbon\Carbon::parse($item->followup_date)->format('d M Y') }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <div class="small text-muted mt-1">Set note + date for Upcoming Tests report</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="lab-empty">No tests found for this invoice.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($reports->isNotEmpty())
        <div class="lab-panel">
            <div class="lab-panel-head">
                <span><i class="fas fa-file-medical me-1"></i> Test Reports ({{ $reports->count() }})</span>
            </div>
            <div class="lab-panel-body p-0">
                <div class="table-responsive">
                    <table class="table crud-table table-hover mb-0">
                        <thead>
                        <tr>
                            <th>Test Name</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($reports as $report)
                            <tr>
                                <td>{{ optional(optional($report->invoiceItem)->product)->name ?? 'N/A' }}</td>
                                <td class="text-end">
                                    <div class="lab-crud-actions justify-content-end">
                                        <a class="lab-btn pdf" target="_blank"
                                           href="{{ route('admin.preview-pdf-report', $report->id) }}" title="PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        <a class="lab-btn edit"
                                           href="{{ route('admin.test_reports.edit', $report->id) }}" title="Edit">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <a class="lab-btn pdf"
                                           href="{{ route('admin.preview-pdf-delete', $report->id) }}"
                                           onclick="return confirm('Delete this report?');" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Modals outside table (Bootstrap needs valid DOM placement) --}}
@foreach($tests as $item)
    <div class="modal fade" id="invoiceItemFollowupModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Test Follow-up · {{ $item->product->name ?? 'N/A' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('admin.lab.followup.update', $item->id) }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="invoice_item_note_{{ $item->id }}" class="form-label">Note</label>
                            <textarea class="form-control" id="invoice_item_note_{{ $item->id }}" name="note" rows="4"
                                      placeholder="e.g. Repeat CBC after 7 days, call patient with result...">{{ $item->note }}</textarea>
                        </div>
                        <div class="mb-0">
                            <label for="invoice_item_followup_date_{{ $item->id }}" class="form-label">Next follow-up date</label>
                            <input type="date" class="form-control lab-followup-date-input"
                                   id="invoice_item_followup_date_{{ $item->id }}" name="followup_date"
                                   value="{{ $item->followup_date ? \Carbon\Carbon::parse($item->followup_date)->format('Y-m-d') : '' }}">
                            <div class="lab-quick-dates" data-target="invoice_item_followup_date_{{ $item->id }}">
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-days="3">+3 days</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-days="7">+7 days</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-days="14">+14 days</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-days="30">+30 days</button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-outline-danger btn-sm"
                                onclick="clearTestFollowup({{ $item->id }})">Clear</button>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Follow-up</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        jQuery(function ($) {
            $('.lab-reagent-select').each(function () {
                $(this).select2({
                    width: '100%',
                    placeholder: $(this).data('placeholder') || 'Select reagents',
                    allowClear: true,
                    minimumResultsForSearch: 0,
                    dropdownParent: $(this).closest('.lab-reagent-box'),
                });
            });
        });

        document.querySelectorAll('.lab-quick-dates').forEach(function (wrap) {
            var targetId = wrap.getAttribute('data-target');
            var input = document.getElementById(targetId);
            if (!input) return;
            wrap.querySelectorAll('[data-days]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var days = parseInt(btn.getAttribute('data-days'), 10);
                    var d = new Date();
                    d.setDate(d.getDate() + days);
                    input.value = d.toISOString().slice(0, 10);
                });
            });
        });

        function clearTestFollowup(itemId) {
            var form = document.querySelector('#invoiceItemFollowupModal' + itemId + ' form');
            if (!form) return;
            form.querySelector('[name="note"]').value = '';
            form.querySelector('[name="followup_date"]').value = '';
            form.submit();
        }

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
