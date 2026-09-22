<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('branch.auth.login');
    }

    public function showRegisterForm()
    {
        return view('branch.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:branches,email',
            'phone'     => 'required|string|unique:branches,phone',
            'address'   => 'required|string',
            'login_id'  => 'required|string|unique:branches,login_id',
            'password'  => 'required|string|min:6|confirmed',
        ]);

        \App\Models\Branch::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'address'   => $request->address,
            'login_id'  => $request->login_id,
            'password'  => \Illuminate\Support\Facades\Hash::make($request->password),
            'is_active' => 0, // Pending approval
        ]);

        return redirect()->route('branch.login')->with('success', 'Registration successful! Your account is pending approval by admin.');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login_id' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::guard('branch')->attempt(['login_id' => $request->login_id, 'password' => $request->password, 'is_active' => 1])) {
            return redirect()->intended(route('branch.dashboard'));
        }

        return back()->withErrors([
            'login_id' => 'The provided credentials do not match our records or account is inactive.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('branch')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('branch.login');
    }
}
