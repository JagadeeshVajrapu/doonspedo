<?php

namespace App\Services\Aadhaar;

use RuntimeException;

class UnconfiguredAadhaarProvider implements AadhaarVerificationProvider
{
    public function isConfigured(): bool
    {
        $provider = trim((string) config('services.aadhaar.provider'));
        $baseUrl = trim((string) config('services.aadhaar.base_url'));
        $apiKey = trim((string) config('services.aadhaar.api_key'));

        return $provider !== '' && $baseUrl !== '' && $apiKey !== '';
    }

    public function requestOtp(string $aadhaarNumber): string
    {
        unset($aadhaarNumber);

        if (!$this->isConfigured()) {
            throw new RuntimeException('Aadhaar OTP verification is not configured. An authorized UIDAI verification provider must be set before this can succeed.');
        }

        throw new RuntimeException('The selected Aadhaar provider is not connected yet. Verification was not completed.');
    }

    public function verifyOtp(string $providerReference, string $otp): bool
    {
        unset($providerReference, $otp);

        throw new RuntimeException('Aadhaar OTP verification is not available. The Aadhaar status was not changed.');
    }
}
