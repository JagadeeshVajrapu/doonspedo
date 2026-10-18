<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\AccountDeletionRequest;
use App\Models\DriverRegistration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AccountDeletionController extends Controller
{
    public function show()
    {
        return view('frontend.account-deletion');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'account_type' => ['required', Rule::in(['customer', 'partner'])],
            'email' => ['nullable', 'email', 'max:255', 'required_without:mobile'],
            'mobile' => ['nullable', 'string', 'max:20', 'required_without:email'],
        ]);

        $email = isset($data['email']) ? strtolower(trim((string) $data['email'])) : null;
        $mobile = isset($data['mobile']) ? preg_replace('/\s+/', '', (string) $data['mobile']) : null;
        if ($mobile === '') {
            $mobile = null;
        }

        $record = AccountDeletionRequest::create([
            'reference_no' => AccountDeletionRequest::makeReference(),
            'account_type' => $data['account_type'],
            'account_id' => $this->resolveAccountId($data['account_type'], $email, $mobile),
            'email' => $email,
            'mobile' => $mobile,
            'status' => AccountDeletionRequest::STATUS_PENDING_VERIFICATION,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);

        $otpSent = false;
        if ($mobile) {
            $otp = (string) random_int(100000, 999999);
            $record->verification_code_hash = Hash::make($otp);
            $record->otp_expires_at = now()->addMinutes(15);
            $record->verification_channel = 'sms';
            $record->save();

            $message = "Your Doonspedo account deletion OTP is {$otp}. Valid for 15 minutes. Do not share this code.";
            $otpSent = (bool) send_sms($mobile, $message);

            if (app()->environment(['local', 'testing'])) {
                $otpSent = true;
                session()->flash('local_deletion_otp', $otp);
            }
        }

        if ($otpSent) {
            return redirect()
                ->route('account.deletion.verify', $record->reference_no)
                ->with('success', 'If the contact details are valid, a verification code has been sent. Enter the code to continue. Reference: '.$record->reference_no);
        }

        // Safe fallback when SMS is unavailable or only email was provided:
        // queue for administrator review without pretending verification succeeded.
        $record->status = AccountDeletionRequest::STATUS_PENDING_REVIEW;
        $record->verification_channel = $mobile ? 'manual' : 'none';
        $record->verification_code_hash = null;
        $record->otp_expires_at = null;
        $record->is_verified = false;
        $record->save();

        return redirect()
            ->route('account.deletion.success', $record->reference_no)
            ->with('success', 'Your deletion request was received and is pending review. Reference: '.$record->reference_no.'. Submitting a request does not mean deletion is complete.');
    }

    public function showVerify(string $reference)
    {
        $requestRecord = AccountDeletionRequest::query()
            ->where('reference_no', $reference)
            ->where('status', AccountDeletionRequest::STATUS_PENDING_VERIFICATION)
            ->firstOrFail();

        return view('frontend.account-deletion-verify', [
            'requestRecord' => $requestRecord,
        ]);
    }

    public function verify(Request $request, string $reference)
    {
        $requestRecord = AccountDeletionRequest::query()
            ->where('reference_no', $reference)
            ->where('status', AccountDeletionRequest::STATUS_PENDING_VERIFICATION)
            ->firstOrFail();

        $data = $request->validate([
            'otp' => ['required', 'string', 'max:12'],
        ]);

        if ($requestRecord->otp_attempts >= 5) {
            return back()->with('error', 'Too many attempts. Please submit a new deletion request.');
        }

        $requestRecord->otp_attempts = (int) $requestRecord->otp_attempts + 1;
        $requestRecord->save();

        $valid = $requestRecord->verification_code_hash
            && $requestRecord->otp_expires_at
            && $requestRecord->otp_expires_at->isFuture()
            && Hash::check($data['otp'], $requestRecord->verification_code_hash);

        if (!$valid) {
            Log::warning('Account deletion OTP mismatch', ['reference' => $requestRecord->reference_no]);

            return back()->with('error', 'Invalid or expired verification code.');
        }

        $accountId = $this->resolveAccountId($requestRecord->account_type, $requestRecord->email, $requestRecord->mobile);

        $requestRecord->forceFill([
            'is_verified' => true,
            'verified_at' => now(),
            'verification_code_hash' => null,
            'otp_expires_at' => null,
            'account_id' => $accountId,
            'status' => AccountDeletionRequest::STATUS_PENDING_REVIEW,
        ])->save();

        return redirect()
            ->route('account.deletion.success', $requestRecord->reference_no)
            ->with('success', 'Identity verified. Your request is now pending administrator review.');
    }

    public function success(string $reference)
    {
        $requestRecord = AccountDeletionRequest::query()
            ->where('reference_no', $reference)
            ->firstOrFail();

        return view('frontend.account-deletion-success', [
            'requestRecord' => $requestRecord,
        ]);
    }

    private function resolveAccountId(string $accountType, ?string $email, ?string $mobile): ?int
    {
        $model = $accountType === 'customer' ? User::query() : DriverRegistration::query();

        $byEmail = $email ? (clone $model)->where('email', $email)->first() : null;
        $byMobile = $mobile ? (clone $model)->where('mobile', $mobile)->first() : null;

        // When both identifiers are supplied they must refer to the same account.
        if ($byEmail && $byMobile) {
            return (int) $byEmail->id === (int) $byMobile->id ? (int) $byEmail->id : null;
        }

        if ($byEmail) {
            return (int) $byEmail->id;
        }

        if ($byMobile) {
            return (int) $byMobile->id;
        }

        return null;
    }
}
