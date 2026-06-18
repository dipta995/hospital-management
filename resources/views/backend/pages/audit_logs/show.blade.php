@extends('backend.layouts.master')

@section('title')
    Change Details
@endsection

@push('styles')
    <style>
        .audit-json {
            background: #0f172a;
            color: #e2e8f0;
            border-radius: 12px;
            font-size: 12px;
            line-height: 1.5;
            max-height: 620px;
            overflow: auto;
            padding: 16px;
            white-space: pre-wrap;
        }
        .audit-change-added { background: #dcfce7; }
        .audit-change-removed { background: #fee2e2; }
        .audit-change-changed { background: #fef3c7; }
        .audit-value {
            max-width: 420px;
            white-space: pre-wrap;
            word-break: break-word;
        }
        .audit-summary-card {
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 14px;
            background: #f8fafc;
        }
    </style>
@endpush

@section('admin-content')
    @php
        $changes = collect($log->changes ?? []);
        $recordName = ['invoice' => 'Invoice Bill', 'recept' => 'Hospital Receipt', 'cost' => 'Cost Entry'][$log->module] ?? ucfirst($log->module);
        $actionName = ['updated' => 'edited', 'deleted' => 'deleted'][$log->action] ?? $log->action;
        $badgeClass = [
            'added' => 'bg-success',
            'removed' => 'bg-danger',
            'changed' => 'bg-warning text-dark',
        ];
        $typeText = [
            'added' => 'Added',
            'removed' => 'Removed',
            'changed' => 'Changed',
        ];
        $friendlyField = function ($field) {
            $clean = preg_replace('/\.\d+\./', '.', (string) $field);
            $clean = preg_replace('/\.\d+$/', '', $clean);
            $labels = [
                'invoice_number' => 'Invoice number',
                'patient_no' => 'Patient ID',
                'patient_name' => 'Patient name',
                'patient_age_year' => 'Patient age',
                'patient_phone' => 'Patient phone',
                'patient_email' => 'Patient email',
                'patient_gender' => 'Patient gender',
                'patient_blood_group' => 'Blood group',
                'patient_address' => 'Patient address',
                'dr_name' => 'Doctor name',
                'dr_refer_id' => 'Doctor reference',
                'refer_id' => 'Referred by',
                'total_amount' => 'Total amount',
                'discount_amount' => 'Discount amount',
                'paid_amount' => 'Paid amount',
                'payment_type' => 'Payment method',
                'delivery_at' => 'Delivery time',
                'creation_date' => 'Posting date',
                'created_date' => 'Posting date',
                'reason' => 'Reason',
                'amount' => 'Amount',
                'account_details' => 'Account details',
                'account_type' => 'Account type',
                'cost_category_id' => 'Cost category',
                'category.name' => 'Cost category name',
                'admin.name' => 'User name',
                'product.name' => 'Test name',
                'service.name' => 'Service name',
                'price' => 'Price',
            ];

            foreach ($labels as $key => $label) {
                if ($clean === $key || str_ends_with($clean, '.'.$key)) {
                    return $label;
                }
            }

            return str($clean)->replace(['_', '.'], [' ', ' > '])->title();
        };
        $friendlyValue = function ($value) {
            if ($value === null || $value === '') {
                return 'Blank';
            }

            if (is_bool($value)) {
                return $value ? 'Yes' : 'No';
            }

            if (is_array($value)) {
                return json_encode($value, JSON_UNESCAPED_UNICODE);
            }

            return $value;
        };
        $shortSummary = function ($values) use ($log) {
            $values = $values ?? [];
            if ($log->module === 'invoice') {
                return ($values['invoice_number'] ?? 'Invoice #'.$log->auditable_id).' - '.($values['patient_name'] ?? 'Unknown patient');
            }
            if ($log->module === 'recept') {
                return 'Receipt #'.$log->auditable_id.' - Total: '.($values['total_amount'] ?? 'N/A');
            }
            if ($log->module === 'cost') {
                return ($values['reason'] ?? 'Cost #'.$log->auditable_id).' - Amount: '.($values['amount'] ?? 'N/A');
            }

            return 'Record #'.$log->auditable_id;
        };
    @endphp

    <div class="container-fluid py-3">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h4 class="mb-1">Change Details</h4>
                        <div class="text-muted">
                            {{ $recordName }} #{{ $log->auditable_id }}
                            was {{ $actionName }} by {{ $log->admin->name ?? 'Unknown' }}
                            on {{ $log->created_at?->format('d M Y h:i A') }}.
                        </div>
                        <div class="text-muted small mt-1">
                            IP: {{ $log->ip_address ?? 'N/A' }}
                        </div>
                    </div>
                    <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-light">Back to Trash History</a>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <div class="audit-summary-card">
                    <div class="text-muted small mb-1">Before</div>
                    <strong>{{ $shortSummary($log->old_values) }}</strong>
                </div>
            </div>
            <div class="col-md-6">
                <div class="audit-summary-card">
                    <div class="text-muted small mb-1">After</div>
                    <strong>{{ $log->action === 'deleted' ? 'Record deleted' : $shortSummary($log->new_values) }}</strong>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h5 class="mb-3">What Changed</h5>
                @if($log->action === 'deleted')
                    <div class="alert alert-danger mb-3">
                        This {{ strtolower($recordName) }} was deleted. The full previous information is saved below so it cannot be hidden.
                    </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                        <tr>
                            <th style="width: 24%;">Item</th>
                            <th>Before</th>
                            <th>After</th>
                            <th style="width: 120px;">Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($log->action === 'deleted' ? collect() : $changes as $change)
                            @php $type = $change['type'] ?? 'changed'; @endphp
                            <tr class="audit-change-{{ $type }}">
                                <td><strong>{{ $friendlyField($change['field'] ?? '') }}</strong></td>
                                <td class="audit-value">{{ $friendlyValue($change['old'] ?? null) }}</td>
                                <td class="audit-value">{{ $friendlyValue($change['new'] ?? null) }}</td>
                                <td>
                                    <span class="badge {{ $badgeClass[$type] ?? 'bg-secondary' }}">{{ $typeText[$type] ?? ucfirst($type) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    {{ $log->action === 'deleted' ? 'Deleted record details are saved in the section below.' : 'No visible changes found.' }}
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <p>
            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#technicalDetails" aria-expanded="false" aria-controls="technicalDetails">
                Show Full Saved Details
            </button>
        </p>

        <div class="collapse" id="technicalDetails">
        <div class="row g-3">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="mb-3">Before Details</h5>
                        <pre class="audit-json">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="mb-3">After Details</h5>
                        <pre class="audit-json">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
@endsection
