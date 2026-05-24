<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPaymentRequest;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->checkGuard();
    }

    public function index()
    {
        $branchId = auth()->user()->branch_id;
        $subscription = SubscriptionService::getCurrentForBranch($branchId);
        $meta = SubscriptionService::getMetaForBranch($branchId);

        $requests = collect();
        if ($subscription) {
            $requests = SubscriptionPaymentRequest::with('submittedByAdmin')
                ->where('subscription_id', $subscription->id)
                ->latest('id')
                ->paginate(15);
        }

        return view('backend.pages.subscriptions.index', [
            'subscription' => $subscription,
            'subscriptionMeta' => $meta,
            'requests' => $requests,
        ]);
    }

    public function updateDate(Request $request)
    {
        if (!auth('admin')->check() || !auth('admin')->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized Access');
        }

        $request->validate([
            'start_date' => 'required|date',
            'payment_amount' => 'required|numeric|min:0',
            'payment_rules' => 'nullable|string',
            'transaction_details_note' => 'nullable|string',
        ]);

        $branchId = auth()->user()->branch_id;
        $current = SubscriptionService::getCurrentForBranch($branchId);
        $subscription = ($current && (int) $current->branch_id === (int) $branchId) ? $current : new Subscription();

        $subscription->branch_id = $branchId;
        $subscription->renewFromDate(Carbon::parse($request->start_date));
        $subscription->payment_amount = $request->payment_amount;
        $subscription->payment_rules = $request->payment_rules;
        $subscription->transaction_details_note = $request->transaction_details_note;
        $subscription->updated_by = auth()->id();
        if (!$subscription->exists) {
            $subscription->created_by = auth()->id();
        }
        $subscription->save();

        return RedirectHelper::routeSuccess('admin.subscriptions.index', 'সাবস্ক্রিপশনের তারিখ ও পেমেন্টের পরিমাণ সফলভাবে হালনাগাদ করা হয়েছে।');
    }

    public function approveRequest($id)
    {
        $requestRow = SubscriptionPaymentRequest::with('subscription')->findOrFail($id);

        if (!is_null($requestRow->subscription->branch_id) && (int) $requestRow->subscription->branch_id !== (int) auth()->user()->branch_id) {
            abort(403, 'Unauthorized Access');
        }

        \DB::transaction(function () use ($requestRow) {
            $requestRow->status = 'approved';
            $requestRow->approved_by = auth()->id();
            $requestRow->approved_at = Carbon::now('Asia/Dhaka');
            $requestRow->reject_reason = null;
            $requestRow->save();

            $subscription = $requestRow->subscription;
            $transactionDate = $requestRow->transaction_date
                ? Carbon::parse($requestRow->transaction_date, 'Asia/Dhaka')->startOfDay()
                : Carbon::now('Asia/Dhaka')->startOfDay();

            $subscription->start_date = $transactionDate->toDateString();
            $subscription->end_date = $transactionDate->copy()->addMonthNoOverflow()->toDateString();
            $subscription->updated_by = auth()->id();
            $subscription->save();
        });

        return RedirectHelper::routeSuccess('admin.subscriptions.index', 'পেমেন্ট অনুমোদন করা হয়েছে। সাবস্ক্রিপশনের মেয়াদ সফলভাবে নবায়ন হয়েছে।');
    }

    public function rejectRequest(Request $request, $id)
    {
        $request->validate([
            'reject_reason' => 'required|string|max:500',
        ]);

        $requestRow = SubscriptionPaymentRequest::with('subscription')->findOrFail($id);

        if (!is_null($requestRow->subscription->branch_id) && (int) $requestRow->subscription->branch_id !== (int) auth()->user()->branch_id) {
            abort(403, 'Unauthorized Access');
        }

        $requestRow->status = 'rejected';
        $requestRow->approved_by = auth()->id();
        $requestRow->approved_at = Carbon::now('Asia/Dhaka');
        $requestRow->reject_reason = $request->reject_reason;
        $requestRow->save();

        return RedirectHelper::routeWarning('admin.subscriptions.index', 'পেমেন্ট অনুরোধটি বাতিল করা হয়েছে।');
    }
}
