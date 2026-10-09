<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomerAadhaarVerification;
use App\Models\CustomerKycSubmission;
use App\Models\User;
use App\Services\Aadhaar\AadhaarVerificationService;
use App\Support\CustomerKycGate;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

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
        return redirect()->route('login')->with('success', 'Logged out successfully.');
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
        $submissions = CustomerKycSubmission::where('user_id', auth()->id())->latest()->get();
        $submission = $submissions->first();
        $aadhaar = CustomerAadhaarVerification::where('user_id', auth()->id())->latest()->first();

        return view('frontend.rider.kyc', compact('submission', 'submissions', 'aadhaar'));
    }

    public function storeKyc(Request $request)
    {
        $user = auth()->user();
        $documentType = (string) $request->input('document_type');
        $documentLabel = trim((string) $request->input('document_label'));
        $documentNumber = preg_replace('/\s+/', '', (string) $request->input('document_number'));
        if ($documentType === 'pan') {
            $documentNumber = strtoupper($documentNumber);
        }
        $request->merge([
            'document_number' => $documentNumber,
            'document_label' => $documentLabel,
        ]);

        $numberRules = ['required', 'string', 'max:80'];
        if ($documentType === 'aadhaar') {
            $numberRules[] = 'regex:/^[0-9]{12}$/';
        } elseif ($documentType === 'pan') {
            $numberRules[] = 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/';
        } else {
            $numberRules[] = 'min:5';
        }

        $existing = CustomerKycSubmission::query()
            ->where('user_id', $user->id)
            ->where('document_type', $documentType)
            ->when($documentType === 'other', function ($query) use ($documentLabel) {
                $query->where('document_label', $documentLabel);
            })
            ->latest()
            ->first();

        if ($existing && $existing->status === 'approved') {
            return back()->with('error', 'This document is already verified.');
        }

        $replacing = $existing && in_array($existing->status, ['pending', 'rejected'], true) && $existing->document_path;
        $request->validate([
            'full_name' => 'required|string|max:255',
            'document_type' => 'required|in:aadhaar,pan,driving_licence,voter_id,other',
            'document_label' => 'required_if:document_type,other|nullable|string|max:80',
            'document_number' => $numberRules,
            'document' => ($replacing ? 'nullable' : 'required').'|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'document_number.regex' => $documentType === 'aadhaar'
                ? 'Enter the 12-digit Aadhaar number.'
                : 'Enter a valid PAN in the format ABCDE1234F.',
            'document.required' => 'Upload a clear photo or PDF of your ID.',
            'document.mimes' => 'Upload a JPG, PNG, or PDF file.',
        ]);

        $storedNumber = $documentType === 'aadhaar'
            ? CustomerKycGate::mask('aadhaar', $documentNumber)
            : $documentNumber;

        $path = $replacing ? $existing->document_path : null;
        if ($request->hasFile('document')) {
            $path = $request->file('document')->store('customer-kyc/'.$user->id, 'local');
        }

        $payload = [
            'full_name' => $request->full_name,
            'document_type' => $documentType,
            'document_label' => $documentType === 'other' ? $documentLabel : null,
            'document_number' => $storedNumber,
            'document_path' => $path,
            'status' => 'pending',
            'rejection_reason' => null,
            'reviewed_at' => null,
        ];

        if ($replacing) {
            $existing->update($payload);
            $message = 'Document updated. Status: Pending Verification.';
        } else {
            CustomerKycSubmission::create($payload + ['user_id' => $user->id]);
            $message = 'Document submitted. Status: Pending Verification.';
        }

        return back()->with('success', $message);
    }

    public function requestAadhaarOtp(Request $request, AadhaarVerificationService $aadhaar)
    {
        $request->validate([
            'aadhaar_number' => 'required|regex:/^[0-9]{12}$/',
        ], [
            'aadhaar_number.regex' => 'Enter the 12-digit Aadhaar number.',
        ]);

        try {
            $aadhaar->request($request->user(), (string) $request->input('aadhaar_number'));
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Aadhaar verification was sent to the provider. Enter the OTP from the authorized Aadhaar service.');
    }

    public function verifyAadhaarOtp(Request $request, AadhaarVerificationService $aadhaar)
    {
        $request->validate([
            'otp' => 'required|regex:/^[0-9]{4,8}$/',
        ]);

        try {
            $aadhaar->verify($request->user(), (string) $request->input('otp'));
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Aadhaar status: Verified.');
    }
}
