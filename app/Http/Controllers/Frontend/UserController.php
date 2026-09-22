<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) return redirect()->route('rider.app');
        if (Auth::guard('admin')->check() || session()->has('driver_id')) return redirect('/')->with('error', 'Please logout from your active account before logging in as a rider.');
        
        return view('frontend.rider-login');
    }

    public function showRegisterForm()
    {
        if (Auth::check()) return redirect()->route('rider.app');
        if (Auth::guard('admin')->check() || session()->has('driver_id')) return redirect('/')->with('error', 'Please logout from your active account before registering as a rider.');
        
        return view('frontend.rider-register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('rider.app')->with('success', 'Logged in successfully!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15|unique:users,mobile',
        ]);

        $user = User::create([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'password' => bcrypt('rider123'), // Default password or handle differently
        ]);

        // After registration, send OTP to verify mobile
        $otp = rand(100000, 999999);
        $message = "Your doonspedo1 login OTP is $otp. Please enter this code to continue. This OTP is valid for 5 minutes. - doonspedo";
        send_sms($user->mobile, $message, '1707177754632189212');

        session(['rider_otp' => $otp, 'rider_mobile' => $user->mobile]);
        session()->save();
        
        return redirect()->route('login.verifyOtpForm')->with('success', 'Registration successful! Please verify your mobile.');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string|max:15',
        ]);

        $user = User::where('mobile', $request->mobile)->first();

        if (!$user) {
            return redirect()->route('register')->with('info', 'Your mobile number is not registered. Please sign up below.');
        }

        // Send actual OTP using TrueBulkSMS
        $otp = rand(100000, 999999);
        $message = "Your doonspedo1 login OTP is $otp. Please enter this code to continue. This OTP is valid for 5 minutes. - doonspedo";
        send_sms($request->mobile, $message, '1707177754632189212');

        session(['rider_otp' => $otp, 'rider_mobile' => $request->mobile]);
        session()->save();
        
        return redirect()->route('login.verifyOtpForm')->with('success', 'OTP sent successfully!');
    }

    public function verifyOtp(Request $request)
    {
        if (Auth::guard('admin')->check() || session()->has('driver_id')) {
            return redirect('/')->with('error', 'Please logout from your active account first.');
        }

        $request->validate([
            'otp'    => 'required|string',
            'mobile' => 'required|string',
        ]);

        if ($request->otp == session('rider_otp')) {
            $user = User::where('mobile', $request->mobile)->first();
            
            if (!$user) {
                return back()->with('error', 'User not found. Please register again.');
            }

            Auth::login($user);
            $request->session()->regenerate();
            
            session()->forget(['rider_otp', 'rider_mobile']);
            session()->save();
            
            return redirect()->route('rider.app')->with('success', 'Logged in successfully!');
        }

        \Illuminate\Support\Facades\Log::warning("OTP mismatch for mobile {$request->mobile}. Expected: " . session('rider_otp') . ", Got: {$request->otp}");
        return back()->with('error', 'Invalid OTP. Please try again.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Logged out successfully.');
    }

    public function editProfile()
    {
        return view('frontend.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|unique:users,email,' . $user->id,
            'mobile' => 'required|string|unique:users,mobile,' . $user->id,
            'photo'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only('name', 'email', 'mobile');

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->photo && file_exists(public_path('uploads/profiles/' . $user->photo))) {
                unlink(public_path('uploads/profiles/' . $user->photo));
            }

            $imageName = time() . '.' . $request->photo->extension();
            $request->photo->move(public_path('uploads/profiles'), $imageName);
            $data['photo'] = $imageName;
        }

        $user->update($data);

        return back()->with('success', 'Profile updated successfully!');
    }
}
