<?php

namespace App\Services\Aadhaar;

interface AadhaarVerificationProvider
{
    public function isConfigured(): bool;

    /**
     * Ask an authorized provider to send an Aadhaar OTP.
     * Returns a provider reference. The OTP itself must not be returned or stored.
     */
    public function requestOtp(string $aadhaarNumber): string;

    public function verifyOtp(string $providerReference, string $otp): bool;
}
