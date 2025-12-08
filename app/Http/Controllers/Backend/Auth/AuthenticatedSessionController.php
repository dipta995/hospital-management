<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Admin;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

    public function change(Request $request)
    {
       return view('backend.auth.change');
    }
    public function changePw(Request $request)
    {
        $id = Auth::guard('admin')->id();
        $request->validate([
            'old_password' => 'required',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/',       // at least 1 uppercase
                'regex:/[a-z]/',       // at least 1 lowercase
                'regex:/[0-9]/',       // at least 1 number
                'regex:/[@$!%*#?&]/',  // at least 1 special char
            ],
        ], [
            'password.min' => 'Password must be at least 8 characters long.',
            'password.regex' => 'Password must contain at least 1 uppercase, 1 lowercase, 1 number, and 1 special character.',
            'password.confirmed' => 'Password confirmation does not match.',
            'old_password.required' => 'Old password is required.'
        ]);
        $admin = Admin::findOrFail($id);

        // Check Old Password
        if (!Hash::check($request->old_password, $admin->password)) {
            return back()->with('error', 'Old password does not match');
        }

        // Update Password
        $admin->password = Hash::make($request->password);
        $admin->save();

        return redirect()->route('admin.home')->with('success', 'Password changed successfully!');
    }

}
