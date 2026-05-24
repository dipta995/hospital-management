<!doctype html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>পেমেন্ট অনুমোদন তালিকা</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body p-4">
                    <h3 class="mb-2">পেমেন্ট অনুমোদন / বাতিল তালিকা</h3>
                    <p class="mb-0 text-muted">এই লিংক থেকে সাবস্ক্রিপশনের সব পেমেন্ট অনুরোধ দেখা যাবে এবং এখান থেকেই অনুমোদন বা বাতিল করা যাবে।</p>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="border rounded p-3 bg-white h-100">
                                <small class="text-muted d-block">শুরুর তারিখ</small>
                                <strong>{{ optional($subscription->start_date)->format('jS F Y') }}</strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 bg-white h-100">
                                <small class="text-muted d-block">শেষ তারিখ</small>
                                <strong>{{ optional($subscription->end_date)->format('jS F Y') }}</strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 bg-white h-100">
                                <small class="text-muted d-block">মোট অনুরোধ</small>
                                <strong>{{ $requests->count() }}</strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 bg-white h-100">
                                <small class="text-muted d-block">নির্ধারিত পেমেন্ট</small>
                                <strong>{{ number_format((float) ($subscription->payment_amount ?? 0), 2) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(session('status'))
                        <div class="alert alert-info">{{ session('status') }}</div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($requests->count())
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
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
                                            @if($item->reject_reason)
                                                <div class="text-danger mt-1"><small>কারণ: {{ $item->reject_reason }}</small></div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->status === 'pending')
                                                <form method="POST" action="{{ route('subscription.payment.list.approve', ['token' => $subscription->public_token, 'requestRow' => $item->id]) }}" class="d-inline-block">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">অনুমোদন</button>
                                                </form>

                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $item->id }}">বাতিল</button>

                                                <div class="modal fade" id="rejectModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form method="POST" action="{{ route('subscription.payment.list.reject', ['token' => $subscription->public_token, 'requestRow' => $item->id]) }}" class="modal-content">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">পেমেন্ট অনুরোধ বাতিল করুন</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <label class="form-label">কারণ লিখুন</label>
                                                                <textarea name="reject_reason" rows="3" class="form-control" required></textarea>
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
                        </div>
                    @else
                        <div class="alert alert-info mb-0">এখনও কোনো পেমেন্ট অনুরোধ জমা হয়নি।</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
