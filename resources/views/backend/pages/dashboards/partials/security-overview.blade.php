@php
    $so = $securityOverview;
    $soAlert = ($so['deletes'] ?? 0) > 0 || ($so['pending_cancel'] ?? 0) > 0 || ($so['cash_short'] ?? 0) > 0;
@endphp
<div class="alert {{ $soAlert ? 'alert-danger' : 'alert-light border' }} shadow-sm mb-3 py-2 px-3">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="small d-flex flex-wrap align-items-center gap-3">
            <strong><i class="fas fa-user-shield me-1"></i> আজকের Security</strong>
            <span>Edit <strong>{{ $so['edits'] }}</strong></span>
            <span class="{{ $so['deletes'] > 0 ? 'text-danger fw-bold' : '' }}">Delete <strong>{{ $so['deletes'] }}</strong></span>
            <span>Discount <strong>৳{{ number_format($so['discount']) }}</strong></span>
            @if($so['tables_ready'])
                <a href="{{ route('admin.invoice-cancel-requests.index') }}" class="{{ $so['pending_cancel'] > 0 ? 'text-danger fw-bold' : 'text-reset' }}">
                    Cancel request <strong>{{ $so['pending_cancel'] }}</strong>
                </a>
                <span class="{{ $so['cash_short'] > 0 ? 'text-danger fw-bold' : '' }}">Cash short <strong>৳{{ number_format($so['cash_short']) }}</strong></span>
            @endif
            @if(($so['owner_phones'] ?? 0) === 0)
                <a href="{{ route('admin.security.settings') }}" class="text-danger">
                    <i class="fas fa-triangle-exclamation"></i> Owner-এর SMS নম্বর সেট করা নেই
                </a>
            @endif
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.security.report') }}" class="btn btn-sm btn-outline-danger">Suspicious Report</a>
            <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-sm btn-outline-secondary">Trash</a>
        </div>
    </div>
</div>
