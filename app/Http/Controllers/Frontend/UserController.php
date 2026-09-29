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

        \Illuminate\Support\Facades\Log::warning('Rider login OTP did not match.');
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

        $documentType = (string) $request->input('document_type');
        $documentNumber = preg_replace('/\s+/', '', (string) $request->input('document_number'));
        if ($documentType === 'pan') {
            $documentNumber = strtoupper($documentNumber);
        }
        $request->merge(['document_number' => $documentNumber]);

        $numberRules = ['required', 'string', 'max:80'];
        if ($documentType === 'aadhaar') {
            $numberRules[] = 'regex:/^[0-9]{12}$/';
        } elseif ($documentType === 'pan') {
            $numberRules[] = 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/';
        } else {
            $numberRules[] = 'min:5';
        }

        $replacingPending = $existing && $existing->status === 'pending' && $existing->document_path;
        $request->validate([
            'full_name' => 'required|string|max:255',
            'document_type' => 'required|in:aadhaar,pan,driving_licence',
            'document_number' => $numberRules,
            'document' => ($replacingPending ? 'nullable' : 'required').'|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'document_number.regex' => $documentType === 'aadhaar'
                ? 'Enter the 12-digit Aadhaar number.'
                : 'Enter a valid PAN in the format ABCDE1234F.',
            'document.required' => 'Upload a clear photo or PDF of your ID.',
        ]);

        $path = $replacingPending ? $existing->document_path : null;
        if ($request->hasFile('document')) {
            try {
                $path = app(\App\Services\CloudinaryStorage::class)->storeUploadedFile($request->file('document'), 'customer-kyc/'.$user->id);
            } catch (\RuntimeException $e) {
                return back()->with('error', $e->getMessage())->withInput();
            }
        }

        $payload = [
            'full_name' => $request->full_name,
            'document_type' => $documentType,
            'document_number' => $documentNumber,
            'document_path' => $path,
            'status' => 'pending',
            'rejection_reason' => null,
            'reviewed_at' => null,
        ];

        if ($replacingPending) {
            $existing->update($payload);
            $message = 'KYC updated. Status: pending review.';
        } else {
            \App\Models\CustomerKycSubmission::create($payload + ['user_id' => $user->id]);
            $message = 'KYC submitted. Status: pending review.';
        }

        return back()->with('success', $message);
    }
}
