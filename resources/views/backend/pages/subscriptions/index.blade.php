@extends('backend.layouts.master')

@section('title')
    সাবস্ক্রিপশন
@endsection

@section('admin-content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <h4 class="card-title">সাবস্ক্রিপশন ব্যবস্থাপনা</h4>
                        @include('backend.layouts.partials.message')

                        @if($subscription)
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <div class="border rounded p-2">
                                        <small class="text-muted">শুরুর তারিখ</small>
                                        <div><strong>{{ $subscription->start_date?->format('jS F Y') }}</strong></div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <div class="border rounded p-2">
                                        <small class="text-muted">শেষ তারিখ</small>
                                        <div><strong>{{ $subscription->end_date?->format('jS F Y') }}</strong></div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <div class="border rounded p-2">
                                        <small class="text-muted">ব্যবহৃত দিন</small>
                                        <div><strong>{{ $subscriptionMeta['days_used'] }}</strong></div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <div class="border rounded p-2">
                                        <small class="text-muted">নির্ধারিত পেমেন্ট</small>
                                        <div><strong>{{ number_format((float) ($subscription->payment_amount ?? 0), 2) }}</strong></div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <div class="border rounded p-2">
                                        <small class="text-muted">অবস্থা</small>
                                        <div>
                                            @if($subscriptionMeta['expired'])
                                                <span class="badge bg-danger">মেয়াদ শেষ</span>
                                            @else
                                                <span class="badge bg-success">সক্রিয় (বাকি {{ $subscriptionMeta['days_left'] }} দিন)</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning mb-0">কোনো সাবস্ক্রিপশন পাওয়া যায়নি। আগে শুরুর তারিখ সেট করুন।</div>
                        @endif

                        @if($subscription)
                            <div class="mt-3 p-3 border rounded bg-light">
                                <h5 class="mb-3">পপ-আপ ছাড়াই পেমেন্ট জমা দিন</h5>
                                <form method="POST" action="{{ route('subscription.payment.public.submit', $subscription->public_token) }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">ট্রানজেকশন আইডি</label>
                                            <input type="text" name="transaction_id" class="form-control" value="{{ old('transaction_id') }}" required>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">ট্রানজেকশনের তারিখ</label>
                                            <input type="date" name="transaction_date" class="form-control" value="{{ old('transaction_date', now('Asia/Dhaka')->format('Y-m-d')) }}" required>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">পরিমাণ</label>
                                            <input type="number" step="0.01" min="0" name="amount" class="form-control" value="{{ old('amount', $subscription->payment_amount) }}" required>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">প্রেরকের নম্বর</label>
                                            <input type="text" name="sender_number" class="form-control" value="{{ old('sender_number') }}" required>
                                        </div>
                                        <div class="col-md-12 mb-2">
                                            <label class="form-label">নোট (ঐচ্ছিক)</label>
                                            <textarea name="note" class="form-control" rows="2">{{ old('note') }}</textarea>
                                        </div>
                                    </div>

                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-2">
                                        <button type="submit" class="btn btn-success">পেমেন্ট জমা দিন</button>
                                    </div>

                                    @if(!empty($subscriptionMeta['payment_url']))
                                        <div class="mt-3">
                                            <label class="form-label">পেমেন্ট করার লিংক</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="paymentFormUrl" readonly value="http://127.0.0.1:8000/subscription/payment/a0d8602c-fa3a-4ed7-bf73-45814612ba20">
                                                <button type="button" class="btn btn-outline-secondary" id="copyPaymentFormUrl">কপি</button>
                                            </div>
                                        </div>
                                    @endif
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                @if(auth('admin')->check() && auth('admin')->user()->hasRole('Super Admin'))
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="mb-3">সাবস্ক্রিপশনের তারিখ সেট / রিসেট করুন</h5>
                            <form method="POST" action="{{ route('admin.subscriptions.date.update') }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label">শুরুর তারিখ</label>
                                        <input type="date" name="start_date" class="form-control" value="{{ old('start_date', optional($subscription?->start_date)->format('Y-m-d')) }}" required>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label">পেমেন্টের পরিমাণ</label>
                                        <input type="number" step="0.01" min="0" name="payment_amount" class="form-control" value="{{ old('payment_amount', $subscription->payment_amount ?? '') }}" required>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label">পেমেন্টের নিয়মাবলি (পপআপে দেখানো হবে)</label>
                                        <textarea class="form-control" name="payment_rules" rows="2" placeholder="উদাহরণ: শেষ তারিখের আগেই পেমেন্ট সম্পন্ন করতে হবে। সঠিক bKash/Nagad ট্রানজেকশন আইডি শেয়ার করতে হবে।">{{ old('payment_rules', $subscription->payment_rules ?? '') }}</textarea>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <label class="form-label">লেনদেনের তথ্য জমার নির্দেশনা</label>
                                        <textarea class="form-control" name="transaction_details_note" rows="2" placeholder="উদাহরণ: ট্রানজেকশন আইডি, পরিমাণ, প্রেরকের নম্বর ও তারিখ লিখে দিন।">{{ old('transaction_details_note', $subscription->transaction_details_note ?? '') }}</textarea>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">সাবস্ক্রিপশন সংরক্ষণ করুন</button>
                            </form>

                            @if(!empty($subscriptionMeta['payment_url']))
                                <div class="mt-3">
                                    <label class="form-label">পাবলিক পেমেন্ট করার লিংক</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="subscriptionPaymentUrl" readonly value="http://127.0.0.1:8000/subscription/payment/a0d8602c-fa3a-4ed7-bf73-45814612ba20">
                                        <button type="button" class="btn btn-outline-secondary" id="copySubscriptionUrl">কপি</button>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">ক্লায়েন্টের পেমেন্ট অনুরোধ</h5>

                        @if($requests instanceof \Illuminate\Pagination\LengthAwarePaginator && $requests->count())
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>ট্রানজেকশন আইডি</th>
                                        <th>তারিখ</th>
                                        <th>পরিমাণ</th>
                                        <th>জমাদানকারী</th>
                                        <th>প্রেরক</th>
                                        <th>অবস্থা</th>
                                        <th>নোট</th>
                                        <th>অ্যাকশন</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($requests as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->transaction_id }}</td>
                                            <td>{{ optional($item->transaction_date)->format('jS F Y') }}</td>
                                            <td>{{ $item->amount }}</td>
                                            <td>
                                                @if($item->submittedByAdmin)
                                                    {{ $item->submittedByAdmin->name }}
                                                @else
                                                    Public Link
                                                @endif
                                            </td>
                                            <td>{{ $item->sender_number }}</td>
                                            <td>
                                                @if($item->status === 'approved')
                                                    <span class="badge bg-success">অনুমোদিত</span>
                                                @elseif($item->status === 'rejected')
                                                    <span class="badge bg-danger">বাতিল</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">অপেক্ষমাণ</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $item->note }}
                                                @if($item->status === 'rejected' && $item->reject_reason)
                                                    <div class="text-danger mt-1"><small>কারণ: {{ $item->reject_reason }}</small></div>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->status === 'pending')
                                                    <form action="{{ route('admin.subscriptions.requests.approve', $item->id) }}" method="POST" class="d-inline-block">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success">অনুমোদন</button>
                                                    </form>

                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $item->id }}">বাতিল</button>

                                                    <div class="modal fade" id="rejectModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <form action="{{ route('admin.subscriptions.requests.reject', $item->id) }}" method="POST" class="modal-content">
                                                                @csrf
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">পেমেন্ট অনুরোধ বাতিল করুন</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <label class="form-label">কারণ</label>
                                                                    <textarea class="form-control" name="reject_reason" rows="3" required></textarea>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বন্ধ করুন</button>
                                                                    <button type="submit" class="btn btn-danger">বাতিল করুন</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-end">
                                    {!! $requests->links() !!}
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info mb-0">এখনও কোনো পেমেন্ট অনুরোধ জমা হয়নি।</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const copyButton = document.getElementById('copySubscriptionUrl');
        if (copyButton) {
            copyButton.addEventListener('click', function () {
                const input = document.getElementById('subscriptionPaymentUrl');
                input.select();
                document.execCommand('copy');
                copyButton.textContent = 'কপি হয়েছে';
                setTimeout(function () {
                    copyButton.textContent = 'কপি';
                }, 1200);
            });
        }

        const copyPaymentFormButton = document.getElementById('copyPaymentFormUrl');
        if (copyPaymentFormButton) {
            copyPaymentFormButton.addEventListener('click', function () {
                const input = document.getElementById('paymentFormUrl');
                input.select();
                document.execCommand('copy');
                copyPaymentFormButton.textContent = 'কপি হয়েছে';
                setTimeout(function () {
                    copyPaymentFormButton.textContent = 'কপি';
                }, 1200);
            });
        }
    });
</script>
@endpush
