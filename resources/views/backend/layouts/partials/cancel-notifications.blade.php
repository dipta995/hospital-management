@php $cancelUrl = route('admin.invoice-cancel-requests.index'); @endphp
@if ($cancelNotes['pending_count'] > 0)
    <a href="{{ $cancelUrl }}" class="dropdown-item py-2 border-bottom text-wrap text-danger fw-semibold bg-danger-subtle">
        <i class="fas fa-ban me-1"></i> {{ $cancelNotes['pending_count'] }}টি Cancel request অনুমোদনের অপেক্ষায়
    </a>
    @foreach ($cancelNotes['pending'] as $note)
        <a href="{{ $cancelUrl }}" class="dropdown-item py-2 border-bottom text-wrap">
            <div class="small"><strong>{{ $note->invoice_number }}</strong> {{ $note->patient_name ? '(' . $note->patient_name . ')' : '' }}
                · ৳{{ number_format((float) $note->total_amount, 2) }}</div>
            <div class="small text-muted">{{ $note->requester->name ?? 'Unknown' }} cancel চেয়েছে · {{ $note->created_at?->diffForHumans() }}</div>
            <div class="small text-muted text-truncate">কারণ: {{ $note->reason }}</div>
        </a>
    @endforeach
@endif
@foreach ($cancelNotes['reviewed'] as $note)
    @php $approved = $note->status === \App\Models\InvoiceCancelRequest::STATUS_APPROVED; @endphp
    <a href="{{ $cancelUrl }}?status=all" class="dropdown-item py-2 border-bottom text-wrap {{ $note->is_unseen ? 'bg-light' : '' }}">
        <div class="small">
            <i class="fas {{ $approved ? 'fa-circle-check text-success' : 'fa-circle-xmark text-danger' }} me-1"></i>
            Invoice <strong>{{ $note->invoice_number }}</strong> cancel request {{ $approved ? 'Approve (বাতিল)' : 'Reject' }} হয়েছে
            @if ($note->is_unseen)<span class="badge bg-primary ms-1">নতুন</span>@endif
        </div>
        <div class="small text-muted">{{ $note->reviewer->name ?? 'Unknown' }} · {{ $note->reviewed_at?->diffForHumans() }}
            @if ($note->review_note) · {{ $note->review_note }}@endif</div>
    </a>
@endforeach
