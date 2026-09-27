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
            'email' => 'required|email|max:255|unique:users,email',
            'mobile' => 'required|string|max:15|unique:users,mobile',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => $request->password,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('rider.app')->with('success', 'Account created. You are signed in.');
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

    public function showKyc()
    {
        $submission = \App\Models\CustomerKycSubmission::where('user_id', auth()->id())->latest()->first();
        return view('frontend.rider.kyc', compact('submission'));
    }

    public function storeKyc(Request $request)
    {
        $user = auth()->user();
        $existing = \App\Models\CustomerKycSubmission::where('user_id', $user->id)->latest()->first();
        if ($existing && $existing->status === 'approved') {
            return back()->with('error', 'Your KYC is already approved.');
        }
        if ($existing && $existing->status === 'pending') {
            return back()->with('error', 'Your KYC is already under review.');
        }

        $request->validate([
            'full_name' => 'required|string|max:255',
            'document_type' => 'required|in:aadhaar,pan,driving_licence',
            'document_number' => 'nullable|string|max:80',
            'document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $path = $request->file('document')->store('customer-kyc/'.$user->id, 'local');

        \App\Models\CustomerKycSubmission::create([
            'user_id' => $user->id,
            'full_name' => $request->full_name,
            'document_type' => $request->document_type,
            'document_number' => $request->document_number,
            'document_path' => $path,
            'status' => 'pending',
        ]);

        return back()->with('success', 'KYC submitted. Status: pending review.');
    }
}
