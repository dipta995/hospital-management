<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    public function __construct()
    {
        //
    }

    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        if (Auth::guard('admin')->check() == 'true') {
            return redirect('/admin');
        }
        return view('backend.auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {

        try {
//            Log::info('Attempting login for email: ' . $request->email);

            if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
                session()->flash('success', 'Logged in Successfully!');
                return redirect()->intended(RouteServiceProvider::ADMIN_HOME);
            } else {
                session()->flash('error', 'Incorrect Email or Password');
                return back();
            }
        } catch (\Exception $e) {
            // Log the error for debugging
//            Log::error('Login attempt failed: ' . $e->getMessage());
return $e;
            // Flash a generic error message to the user
            session()->flash('error', 'An error occurred while trying to log in. Please try again later.');
            return back();
        }
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }
}
