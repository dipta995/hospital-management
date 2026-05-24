<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionPaymentRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SubscriptionPublicController extends Controller
{
    public function show($token)
    {
        $subscription = Subscription::where('public_token', $token)->firstOrFail();

        return view('subscription.public-payment', [
            'subscription' => $subscription,
        ]);
    }

    public function store(Request $request, $token)
    {
        $subscription = Subscription::where('public_token', $token)->firstOrFail();

        $request->validate([
            'transaction_id' => 'required|string|max:120',
            'transaction_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'sender_number' => 'required|string|max:40',
            'note' => 'nullable|string|max:1000',
        ]);

        SubscriptionPaymentRequest::create([
            'subscription_id' => $subscription->id,
            'admin_id' => auth('admin')->id(),
            'transaction_id' => $request->transaction_id,
            'transaction_date' => $request->transaction_date,
            'amount' => $request->amount,
            'sender_number' => $request->sender_number,
            'note' => $request->note,
            'status' => 'pending',
            'submitted_at' => Carbon::now('Asia/Dhaka'),
        ]);

        return back()->with('success', 'লেনদেনের তথ্য সফলভাবে জমা হয়েছে। অনুগ্রহ করে অ্যাডমিন অনুমোদনের জন্য অপেক্ষা করুন।');
    }

    public function paymentList($token)
    {
        $subscription = Subscription::where('public_token', $token)->firstOrFail();
        $requests = SubscriptionPaymentRequest::with('submittedByAdmin')
            ->where('subscription_id', $subscription->id)
            ->latest('id')
            ->get();

        return view('subscription.payment_list', [
            'subscription' => $subscription,
            'requests' => $requests,
        ]);
    }

    public function approveFromList($token, SubscriptionPaymentRequest $requestRow)
    {
        $subscription = Subscription::where('public_token', $token)->firstOrFail();

        if ((int) $requestRow->subscription_id !== (int) $subscription->id) {
            abort(404);
        }

        if ($requestRow->status !== 'pending') {
            return back()->with('status', 'এই পেমেন্ট অনুরোধটি ইতোমধ্যে প্রক্রিয়া করা হয়েছে।');
        }

        \DB::transaction(function () use ($requestRow) {
            $requestRow->status = 'approved';
            $requestRow->approved_at = Carbon::now('Asia/Dhaka');
            $requestRow->reject_reason = null;
            $requestRow->save();

            $subscription = $requestRow->subscription;
            $transactionDate = $requestRow->transaction_date
                ? Carbon::parse($requestRow->transaction_date, 'Asia/Dhaka')->startOfDay()
                : Carbon::now('Asia/Dhaka')->startOfDay();

            $subscription->start_date = $transactionDate->toDateString();
            $subscription->end_date = $transactionDate->copy()->addMonthNoOverflow()->toDateString();
            $subscription->save();
        });

        return back()->with('success', 'পেমেন্ট অনুমোদন করা হয়েছে এবং সাবস্ক্রিপশনের মেয়াদ নবায়ন হয়েছে।');
    }

    public function rejectFromList(Request $request, $token, SubscriptionPaymentRequest $requestRow)
    {
        $subscription = Subscription::where('public_token', $token)->firstOrFail();

        if ((int) $requestRow->subscription_id !== (int) $subscription->id) {
            abort(404);
        }

        $request->validate([
            'reject_reason' => 'required|string|max:500',
        ]);

        if ($requestRow->status !== 'pending') {
            return back()->with('status', 'এই পেমেন্ট অনুরোধটি ইতোমধ্যে প্রক্রিয়া করা হয়েছে।');
        }

        $requestRow->status = 'rejected';
        $requestRow->approved_at = Carbon::now('Asia/Dhaka');
        $requestRow->reject_reason = $request->reject_reason;
        $requestRow->save();

        return back()->with('status', 'পেমেন্ট অনুরোধটি বাতিল করা হয়েছে।');
    }
}
