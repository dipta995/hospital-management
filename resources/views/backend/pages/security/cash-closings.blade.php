@extends('backend.layouts.master')

@section('title')
    Cash Closing
@endsection

@php
    $tk = fn ($value) => '৳' . number_format((float) $value, 2);
@endphp

@section('admin-content')
    <div class="container-fluid py-3">
        @include('backend.layouts.partials.message')

        @if(!$tableReady)
            <div class="alert alert-warning">
                Security tables এখনো install করা হয়নি। Support-এ যোগাযোগ করুন।
            </div>
        @else
            <div class="row g-3 mb-3">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="mb-1"><i class="fas fa-cash-register text-success"></i> আমার আজকের Cash Closing</h5>
                            <p class="text-muted small mb-3">{{ \Carbon\Carbon::parse($today)->format('l, d M Y') }} — দিন শেষে হাতে থাকা টাকা গুনে লিখুন।</p>

                            <div class="d-flex justify-content-between border-bottom py-2">
                                <span>System অনুযায়ী আমি নিয়েছি ({{ $myCollection['count'] }} payment)</span>
                                <strong>{{ $tk($myCollection['amount']) }}</strong>
                            </div>
                            @foreach($myCollection['breakdown'] as $method => $amount)
                                <div class="d-flex justify-content-between small text-muted py-1 ps-3">
                                    <span>{{ $method }}</span><span>{{ $tk($amount) }}</span>
                                </div>
                            @endforeach

                            @if($myClosing)
                                <div class="alert {{ $myClosing->difference < 0 ? 'alert-danger' : 'alert-success' }} mt-3 mb-0">
                                    <strong>আজকের closing হয়ে গেছে</strong> ({{ $myClosing->created_at->timezone('Asia/Dhaka')->format('h:i A') }})<br>
                                    System {{ $tk($myClosing->system_amount) }} · গুনে পাওয়া {{ $tk($myClosing->counted_amount) }} ·
                                    পার্থক্য <strong>{{ $tk($myClosing->difference) }}</strong>
                                    @if($myCollection['amount'] > (float) $myClosing->system_amount)
                                        <div class="mt-1 text-danger">
                                            Closing-এর পরে আরও {{ $tk($myCollection['amount'] - (float) $myClosing->system_amount) }} নেওয়া হয়েছে।
                                        </div>
                                    @endif
                                </div>
                            @else
                                <form method="POST" action="{{ route('admin.cash-closings.store') }}" class="mt-3"
                                      onsubmit="return confirm('Closing করার পর আজকের আপনার invoice-গুলো lock হয়ে যাবে। নিশ্চিত?')">
                                    @csrf
                                    <div class="mb-2">
                                        <label class="form-label" for="counted_amount">হাতে গুনে পাওয়া মোট টাকা (Cash + bKash/Nagad/Bank) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0" name="counted_amount" id="counted_amount" class="form-control"
                                               value="{{ old('counted_amount') }}" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label" for="note">Note</label>
                                        <textarea name="note" id="note" rows="2" class="form-control" maxlength="1000"
                                                  placeholder="কম/বেশি হলে কারণ লিখুন">{{ old('note') }}</textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success"><i class="fas fa-lock"></i> Close Today</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                @if($canApprove)
                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                    <h5 class="mb-0"><i class="fas fa-users text-primary"></i> Staff-wise Collection</h5>
                                    <form method="GET" class="d-flex gap-2">
                                        <input type="date" name="date" value="{{ $date }}" class="form-control form-control-sm">
                                        <button class="btn btn-sm btn-primary">Show</button>
                                    </form>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead>
                                        <tr>
                                            <th>Staff</th>
                                            <th class="text-end">System (এখন)</th>
                                            <th class="text-end">Closing-এ গুনেছে</th>
                                            <th class="text-end">পার্থক্য</th>
                                            <th>Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($staffRows as $row)
                                            @php $closing = $row['closing']; @endphp
                                            <tr>
                                                <td>{{ $row['admin']->name }}</td>
                                                <td class="text-end">{{ $tk($row['live']['amount']) }}</td>
                                                <td class="text-end">{{ $closing ? $tk($closing->counted_amount) : '—' }}</td>
                                                <td class="text-end {{ $closing && $closing->difference < 0 ? 'text-danger fw-bold' : '' }}">
                                                    {{ $closing ? $tk($closing->difference) : '—' }}
                                                    @if($closing && $row['live']['amount'] > (float) $closing->system_amount)
                                                        <div class="small text-danger">+{{ $tk($row['live']['amount'] - (float) $closing->system_amount) }} after closing</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(!$closing)
                                                        <span class="badge bg-warning text-dark">Not closed</span>
                                                    @elseif($closing->status === 'verified')
                                                        <span class="badge bg-success">Verified</span>
                                                    @else
                                                        <span class="badge bg-info text-dark">Closed</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5" class="text-center text-muted py-3">এই দিনে কোনো collection নেই।</td></tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body table-responsive">
                    <h5 class="mb-3">Closing History</h5>
                    <table class="table table-hover align-middle">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Staff</th>
                            <th class="text-end">System</th>
                            <th class="text-end">Counted</th>
                            <th class="text-end">Difference</th>
                            <th>Note</th>
                            <th>Status</th>
                            @if($canApprove)<th class="text-end">Action</th>@endif
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($history as $item)
                            <tr>
                                <td>{{ $item->closing_date->format('d M Y') }}</td>
                                <td>{{ $item->admin->name ?? 'Unknown' }}</td>
                                <td class="text-end">{{ $tk($item->system_amount) }}</td>
                                <td class="text-end">{{ $tk($item->counted_amount) }}</td>
                                <td class="text-end {{ $item->difference < 0 ? 'text-danger fw-bold' : ($item->difference > 0 ? 'text-warning' : 'text-success') }}">
                                    {{ $tk($item->difference) }}
                                </td>
                                <td class="small" style="max-width: 220px;">{{ $item->note }}</td>
                                <td>
                                    @if($item->status === 'verified')
                                        <span class="badge bg-success">Verified</span>
                                        <div class="small text-muted">{{ $item->verifier->name ?? '' }}</div>
                                        @if($item->verify_note)<div class="small">{{ $item->verify_note }}</div>@endif
                                    @else
                                        <span class="badge bg-info text-dark">Closed</span>
                                    @endif
                                </td>
                                @if($canApprove)
                                    <td class="text-end">
                                        @if($item->status !== 'verified')
                                            <form method="POST" action="{{ route('admin.cash-closings.verify', $item->id) }}"
                                                  onsubmit="const note = prompt('Verify note (optional):'); if (note === null) return false; this.verify_note.value = note; return true;">
                                                @csrf
                                                <input type="hidden" name="verify_note" value="">
                                                <button class="btn btn-sm btn-outline-success"><i class="fas fa-check-double"></i> Cash বুঝে পেয়েছি</button>
                                            </form>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr><td colspan="{{ $canApprove ? 8 : 7 }}" class="text-center text-muted py-4">এখনো কোনো closing নেই।</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                    @if(method_exists($history, 'links'))
                        {{ $history->links() }}
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection
