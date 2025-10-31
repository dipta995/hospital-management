<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (Auth::guard('web')) {
            if (! $request->expectsJson()) {
                return route('login');
            }
        }elseif (Auth::guard('admin')) {
            if (! $request->expectsJson()) {
                return route('admin.login');
            }
        }else {

        }    }
}
