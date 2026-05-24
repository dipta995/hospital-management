<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>সাবস্ক্রিপশন পেমেন্ট জমা</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="mb-3">সাবস্ক্রিপশন পেমেন্ট জমা</h4>

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
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

                    <div class="mb-3 p-3 border rounded bg-white">
                        <h6 class="mb-1">বর্তমান সাবস্ক্রিপশন সময়সীমা</h6>
                        <div>শুরুর তারিখ: <strong>{{ optional($subscription->start_date)->format('jS F Y') }}</strong></div>
                        <div>শেষ তারিখ: <strong>{{ optional($subscription->end_date)->format('jS F Y') }}</strong></div>
                        <div>নির্ধারিত পেমেন্ট: <strong>{{ number_format((float) ($subscription->payment_amount ?? 0), 2) }}</strong></div>
                    </div>

                    @if($subscription->payment_rules)
                        <div class="alert alert-warning">
                            <strong>পেমেন্টের নিয়মাবলি:</strong><br>
                            {!! nl2br(e($subscription->payment_rules)) !!}
                        </div>
                    @endif

                    @if($subscription->transaction_details_note)
                        <div class="alert alert-info">
                            <strong>লেনদেনের তথ্য জমার নির্দেশনা:</strong><br>
                            {!! nl2br(e($subscription->transaction_details_note)) !!}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('subscription.payment.public.submit', $subscription->public_token) }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ট্রানজেকশন আইডি</label>
                                <input type="text" name="transaction_id" class="form-control" value="{{ old('transaction_id') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ট্রানজেকশনের তারিখ</label>
                                <input type="date" name="transaction_date" class="form-control" value="{{ old('transaction_date') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">পরিমাণ</label>
                                <input type="number" step="0.01" min="0" name="amount" class="form-control" value="{{ old('amount', $subscription->payment_amount) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">প্রেরকের নম্বর</label>
                                <input type="text" name="sender_number" class="form-control" value="{{ old('sender_number') }}" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">নোট (ঐচ্ছিক)</label>
                                <textarea name="note" class="form-control" rows="3">{{ old('note') }}</textarea>
                            </div>
                        </div>

                        <button class="btn btn-primary" type="submit">লেনদেনের তথ্য জমা দিন</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
