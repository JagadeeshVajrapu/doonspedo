<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DriverRegistration;
use Illuminate\Support\Facades\Auth;

class DriverController extends Controller
{
    public function showRegisterForm()
    {
        if (session()->has('driver_id')) return redirect()->route('driver.dashboard');
        if (Auth::guard('admin')->check() || Auth::check()) return redirect('/')->with('error', 'Please logout from your active account before registering/logging in as a driver.');
        
        $branches = \App\Models\Branch::where('is_active', 1)->get();
        $categories = \App\Models\VehicleCategory::where('is_active', 1)->get();
        return view('frontend.driver-register', compact('branches', 'categories'));
    }

    public function showLoginForm()
    {
        if (session()->has('driver_id')) return redirect()->route('driver.dashboard');
        if (Auth::guard('admin')->check() || Auth::check()) return redirect('/')->with('error', 'Please logout from your active account before logging in as a driver.');
        
        $branches = \App\Models\Branch::where('is_active', 1)->get();
        return view('frontend.driver-login', compact('branches'));
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string|max:15',
            'branch_id' => 'required|exists:branches,id',
        ]);

        $driver = DriverRegistration::where('mobile', $request->mobile)
            ->where('branch_id', $request->branch_id)
            ->first();

        if (!$driver) {
            return back()->with('error', 'Mobile number not registered with this branch. Please check and try again.');
        }

        // Send actual OTP using TrueBulkSMS
        $otp = rand(100000, 999999);
        $message = "Your doonspedo1 Driver login OTP is $otp. \nEnter this code to access your account.\nOTP valid for 5 minutes.";
        send_sms($request->mobile, $message, '1707177754647058734');
        
        session(['otp' => $otp, 'mobile' => $request->mobile, 'branch_id' => $request->branch_id]);
        session()->save();

        $success = 'OTP sent successfully!';
        // Local/dev only: surface OTP so QA can login without SMS delivery
        if (app()->environment(['local', 'testing'])) {
            $success .= ' (Local OTP: ' . $otp . ')';
        }
        
        return redirect()->route('driver.login.verifyOtpForm')->with('success', $success);
    }

    public function verifyOtp(Request $request)
    {
        if (Auth::guard('admin')->check() || Auth::check()) {
            return redirect('/')->with('error', 'Please logout from your active account first.');
        }

        $request->validate([
            'otp'    => 'required|string',
            'mobile' => 'required|string',
        ]);

        if ($request->otp == session('otp')) {
            $driver = DriverRegistration::where('mobile', $request->mobile)
                ->where('branch_id', session('branch_id'))
                ->first();
            
            if (!$driver) {
                return back()->with('error', 'Driver account not found.');
            }

            // Log in the driver (using a custom session for now since we don't have a full Auth guard setup)
            session(['driver_id' => $driver->id, 'driver_name' => $driver->name, 'branch_id' => $driver->branch_id]);
            $request->session()->regenerate();
            
            session()->forget('otp');
            session()->save();
            
            return redirect()->route('driver.dashboard')->with('success', 'Logged in successfully!');
        }

        \Illuminate\Support\Facades\Log::warning("Driver OTP mismatch for mobile {$request->mobile}. Expected: " . session('otp') . ", Got: {$request->otp}");
        return back()->with('error', 'Invalid OTP. Please try again.');
    }

    public function register(Request $request)
    {
        $request->validate([
            'branch_id'      => 'required|exists:branches,id',
            'name'           => 'required|string|max:255',
            'mobile'         => 'required|string|max:15|unique:driver_registrations,mobile',
            'email'          => 'required|email|max:255|unique:driver_registrations,email',
            'vehicle_type'   => 'required|string',
            'vehicle_number' => 'required|string|max:50',
            'license_number' => 'required|string|max:50',
            'city'           => 'required|string|max:100',
        ]);

        $driver = DriverRegistration::create($request->only([
            'branch_id', 'name', 'mobile', 'email', 'vehicle_type',
            'vehicle_number', 'license_number', 'city'
        ]));

        // Send Registration OTP
        $otp = rand(100000, 999999);
        $message = "Welcome to doonspedo1 Driver!\nYour OTP for registration is $otp. \nDo not share this with anyone.\nValid for 5 minutes.";
        send_sms($driver->mobile, $message, '1707177754641313520');

        session(['otp' => $otp, 'mobile' => $driver->mobile, 'branch_id' => $driver->branch_id]);
        session()->save();

        return redirect()->route('driver.login.verifyOtpForm')
            ->with('success', 'Registration successful! Please verify your mobile number.');
    }
}

