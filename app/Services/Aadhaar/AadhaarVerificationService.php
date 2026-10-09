<?php

namespace App\Services\Aadhaar;

use App\Models\CustomerAadhaarVerification;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use RuntimeException;

class AadhaarVerificationService
{
    public const MAX_REQUESTS_PER_HOUR = 3;

    public const MAX_ATTEMPTS = 5;

    public const EXPIRES_MINUTES = 10;

    public function __construct(private readonly AadhaarVerificationProvider $provider)
    {
    }

    public function request(User $user, string $aadhaarNumber): CustomerAadhaarVerification
    {
        $digits = preg_replace('/\D/', '', $aadhaarNumber) ?? '';
        if (!preg_match('/^\d{12}$/', $digits)) {
            throw new RuntimeException('Enter the 12-digit Aadhaar number.');
        }

        $key = 'aadhaar-otp:'.$user->id;
        if (RateLimiter::tooManyAttempts($key, self::MAX_REQUESTS_PER_HOUR)) {
            throw new RuntimeException('Too many Aadhaar verification attempts. Try again later.');
        }

        $recent = CustomerAadhaarVerification::query()
            ->where('user_id', $user->id)
            ->where('created_at', '>=', now()->subHour())
            ->count();
        if ($recent >= self::MAX_REQUESTS_PER_HOUR) {
            throw new RuntimeException('Too many Aadhaar verification attempts. Try again later.');
        }

        RateLimiter::hit($key, 3600);

        $reference = $this->provider->requestOtp($digits);

        return CustomerAadhaarVerification::create([
            'user_id' => $user->id,
            'aadhaar_hash' => hash_hmac('sha256', $digits, (string) config('app.key')),
            'aadhaar_last4' => substr($digits, -4),
            'provider_reference' => $reference,
            'status' => 'pending',
            'attempts' => 0,
            'expires_at' => now()->addMinutes(self::EXPIRES_MINUTES),
        ]);
    }

    public function verify(User $user, string $otp): CustomerAadhaarVerification
    {
        $otp = trim($otp);
        if (!preg_match('/^\d{4,8}$/', $otp)) {
            throw new RuntimeException('Enter the Aadhaar OTP.');
        }

        $record = CustomerAadhaarVerification::query()
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->latest('id')
            ->first();

        if (!$record) {
            throw new RuntimeException('Start Aadhaar verification before entering the OTP.');
        }

        if ($record->expires_at && $record->expires_at->isPast()) {
            $record->update(['status' => 'expired']);
            throw new RuntimeException('This Aadhaar OTP has expired. Start verification again.');
        }

        if ($record->attempts >= self::MAX_ATTEMPTS) {
            $record->update(['status' => 'failed']);
            throw new RuntimeException('Too many incorrect OTP attempts. Start verification again.');
        }

        $record->increment('attempts');
        $record->refresh();

        $verified = $this->provider->verifyOtp((string) $record->provider_reference, $otp);
        if (!$verified) {
            if ($record->attempts >= self::MAX_ATTEMPTS) {
                $record->update(['status' => 'failed']);
            }
            throw new RuntimeException('The Aadhaar OTP is not valid.');
        }

        $record->update([
            'status' => 'verified',
            'verified_at' => now(),
        ]);

        return $record->refresh();
    }
}
