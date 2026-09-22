<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        if (Auth::check()) {
            return redirect('/')->with('error', 'Please logout from customer account before logging into admin.');
        }
        if (session()->has('driver_id')) {
            return redirect('/')->with('error', 'Please logout from driver account before logging into admin.');
        }
        
        return view('backend.login');
    }

    public function login(Request $request)
    {
        if (Auth::check() || session()->has('driver_id')) {
            return back()->withErrors(['email' => 'Please logout from other active accounts first.']);
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('/admin');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
