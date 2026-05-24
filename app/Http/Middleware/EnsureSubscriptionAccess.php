<?php

namespace App\Http\Middleware;

use App\Services\SubscriptionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscriptionAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('admin')->check()) {
            return $next($request);
        }

        $admin = Auth::guard('admin')->user();
        $meta = SubscriptionService::getMetaForBranch($admin->branch_id);

        view()->share('subscriptionMeta', $meta);

        if ($meta['expired'] && !$request->routeIs('admin.subscriptions.*') && !$request->routeIs('admin.logout.submit')) {
            $status = '<div class="alert alert-danger alert-dismissible show" role="alert">আপনার সাবস্ক্রিপশনের মেয়াদ শেষ হয়েছে। সফটওয়্যার ব্যবহার চালিয়ে যেতে পেমেন্টের তথ্য জমা দিন এবং অনুমোদনের জন্য অপেক্ষা করুন।<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

            return redirect()->route('admin.subscriptions.index')->with('status', $status);
        }

        return $next($request);
    }
}
