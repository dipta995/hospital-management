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

        if ($meta['expired'] && !$this->isAllowedWhileExpired($request, $admin)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 402,
                    'message' => 'সাবস্ক্রিপশনের মেয়াদ শেষ হয়ে গেছে। সফটওয়্যার ব্যবহার করতে পেমেন্ট করুন।',
                    'redirect' => route('admin.subscriptions.index'),
                ], 402);
            }

            $status = view('backend.layouts.partials.subscription-expired-alert')->render();

            return redirect()->route('admin.subscriptions.index')->with('status', $status);
        }

        return $next($request);
    }

    /**
     * Super Admin keeps the system-update routes so a missing schema can still be installed while expired.
     */
    private function isAllowedWhileExpired(Request $request, $admin): bool
    {
        if ($request->routeIs('admin.subscriptions.*', 'admin.logout.submit')) {
            return true;
        }

        return $request->routeIs('admin.system.*') && $admin->hasRole('Super Admin');
    }
}
