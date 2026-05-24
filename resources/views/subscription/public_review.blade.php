<!doctype html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>পেমেন্ট রিভিউ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h3 class="mb-3">পেমেন্ট রিভিউ</h3>

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

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100 bg-white">
                                <small class="text-muted d-block">ট্রানজেকশন আইডি</small>
                                <strong>{{ $requestRow->transaction_id }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100 bg-white">
                                <small class="text-muted d-block">ট্রানজেকশনের তারিখ</small>
                                <strong>{{ optional($requestRow->transaction_date)->format('jS F Y') }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100 bg-white">
                                <small class="text-muted d-block">পরিমাণ</small>
                                <strong>{{ $requestRow->amount }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100 bg-white">
                                <small class="text-muted d-block">প্রেরকের নম্বর</small>
                                <strong>{{ $requestRow->sender_number }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100 bg-white">
                                <small class="text-muted d-block">সাবস্ক্রিপশনের বর্তমান শেষ তারিখ</small>
                                <strong>{{ optional($requestRow->subscription->end_date)->format('jS F Y') }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100 bg-white">
                                <small class="text-muted d-block">অবস্থা</small>
                                @if($requestRow->status === 'approved')
                                    <span class="badge bg-success">অনুমোদিত</span>
                                @elseif($requestRow->status === 'rejected')
                                    <span class="badge bg-danger">বাতিল</span>
                                @else
                                    <span class="badge bg-warning text-dark">অপেক্ষমাণ</span>
                                @endif
                            </div>
                        </div>
                        @if($requestRow->note)
                            <div class="col-12">
                                <div class="border rounded p-3 bg-white">
                                    <small class="text-muted d-block">নোট</small>
                                    <strong>{{ $requestRow->note }}</strong>
                                </div>
                            </div>
                        @endif
                        @if($requestRow->reject_reason)
                            <div class="col-12">
                                <div class="border rounded p-3 bg-danger-subtle text-danger-emphasis">
                                    <small class="d-block">বাতিলের কারণ</small>
                                    <strong>{{ $requestRow->reject_reason }}</strong>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if($requestRow->status === 'pending')
                        <div class="d-flex flex-wrap gap-2">
                            <form method="POST" action="{{ URL::signedRoute('subscription.review.public.approve', ['requestRow' => $requestRow->id]) }}">
                                @csrf
                                <button type="submit" class="btn btn-success">পেমেন্ট অনুমোদন করুন</button>
                            </form>

                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">পেমেন্ট বাতিল করুন</button>
                        </div>

                        <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ URL::signedRoute('subscription.review.public.reject', ['requestRow' => $requestRow->id]) }}" class="modal-content">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">পেমেন্ট বাতিল করুন</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <label class="form-label">কারণ লিখুন</label>
                                        <textarea name="reject_reason" rows="3" class="form-control" required></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বন্ধ করুন</button>
                                        <button type="submit" class="btn btn-danger">বাতিল নিশ্চিত করুন</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
