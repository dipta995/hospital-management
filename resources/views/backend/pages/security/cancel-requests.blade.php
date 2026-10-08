@extends('backend.layouts.master')

@section('title')
    Invoice Cancel Requests
@endsection

@section('admin-content')
    <div class="container-fluid py-3">
        @include('backend.layouts.partials.message')

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div>
                        <h4 class="mb-1">Invoice Cancel Requests
                            @if($pendingCount > 0)
                                <span class="badge bg-danger align-middle">{{ $pendingCount }} pending</span>
                            @endif
                        </h4>
                        <p class="text-muted mb-0">
                            @if($canApprove)
                                Staff সরাসরি invoice মুছতে পারে না। আপনি approve করলে invoice বাতিল হবে এবং পুরো তথ্য Trash-এ থাকবে।
                            @else
                                আপনার পাঠানো cancel request-গুলো। Owner বা Super Admin approve করলে invoice বাতিল হবে।
                            @endif
                        </p>
                    </div>
                </div>

                @if(!$tableReady)
                    <div class="alert alert-warning mb-0">
                        Security tables এখনো install করা হয়নি। Support-এ যোগাযোগ করুন।
                    </div>
                @else
                    <form method="GET" class="d-flex flex-wrap gap-2">
                        @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'All'] as $value => $label)
                            <button type="submit" name="status" value="{{ $value }}"
                                    class="btn btn-sm {{ request('status', 'pending') === $value ? 'btn-primary' : 'btn-outline-primary' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </form>
                @endif
            </div>
        </div>

        @if($tableReady)
            <div class="card border-0 shadow-sm">
                <div class="card-body table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                        <tr>
                            <th>Requested</th>
                            <th>Invoice</th>
                            <th>Bill / Paid</th>
                            <th>Requested By</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($requests as $item)
                            <tr>
                                <td>{{ $item->created_at?->timezone('Asia/Dhaka')->format('d M Y h:i A') }}</td>
                                <td>
                                    <strong>{{ $item->invoice_number }}</strong>
                                    <div class="small text-muted">{{ $item->patient_name }}</div>
                                    @if($item->invoice)
                                        <a href="{{ route('admin.invoices.show', $item->invoice_id) }}" class="small">View invoice</a>
                                    @endif
                                </td>
                                <td>৳{{ number_format($item->total_amount, 2) }}<div class="small text-muted">Paid ৳{{ number_format($item->paid_amount, 2) }}</div></td>
                                <td>{{ $item->requester->name ?? 'Unknown' }}</td>
                                <td class="small" style="max-width: 280px;">{{ $item->reason }}</td>
                                <td>
                                    @php
                                        $statusClass = ['pending' => 'bg-warning text-dark', 'approved' => 'bg-danger', 'rejected' => 'bg-secondary'][$item->status] ?? 'bg-light text-dark';
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ ucfirst($item->status) }}</span>
                                    @if($item->reviewer)
                                        <div class="small text-muted">
                                            by {{ $item->reviewer->name }} · {{ $item->reviewed_at?->timezone('Asia/Dhaka')->format('d M h:i A') }}
                                        </div>
                                    @endif
                                    @if($item->review_note)
                                        <div class="small">{{ $item->review_note }}</div>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($canApprove && $item->isPending())
                                        <form method="POST" action="{{ route('admin.invoice-cancel-requests.approve', $item->id) }}" class="d-inline"
                                              onsubmit="return confirm('Invoice {{ $item->invoice_number }} বাতিল হবে (payment ও cost সহ)। পুরো তথ্য Trash-এ থাকবে। Approve করবেন?')">
                                            @csrf
                                            <input type="hidden" name="review_note" value="">
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-check"></i> Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.invoice-cancel-requests.reject', $item->id) }}" class="d-inline"
                                              onsubmit="const note = prompt('Reject করার কারণ (optional):'); if (note === null) return false; this.review_note.value = note; return true;">
                                            @csrf
                                            <input type="hidden" name="review_note" value="">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="fas fa-times"></i> Reject</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">কোনো cancel request নেই।</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>

                    {{ $requests->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection
