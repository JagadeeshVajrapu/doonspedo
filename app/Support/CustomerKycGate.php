<?php

namespace App\Support;

use App\Models\CustomerAadhaarVerification;
use App\Models\CustomerKycSubmission;
use App\Models\User;

class CustomerKycGate
{
    /** Test seam. Production leaves this null and reads the admin setting. */
    public static ?bool $requiredOverride = null;

    public static function required(): bool
    {
        if (self::$requiredOverride !== null) {
            return self::$requiredOverride;
        }

        return (string) get_settings('customer_kyc_required', '0') === '1';
    }

    public static function isVerified(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        $aadhaarVerified = CustomerAadhaarVerification::query()
            ->where('user_id', $user->id)
            ->where('status', 'verified')
            ->exists();

        if ($aadhaarVerified) {
            return true;
        }

        return CustomerKycSubmission::query()
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->exists();
    }

    public static function bookingMessage(?User $user): ?string
    {
        if (!self::required() || self::isVerified($user)) {
            return null;
        }

        return 'Complete customer KYC before booking a ride.';
    }

    public static function mask(?string $type, ?string $number): string
    {
        $number = trim((string) $number);
        if ($number === '') {
            return '';
        }

        if ($type === 'aadhaar') {
            $digits = preg_replace('/\D/', '', $number) ?? '';
            $last = substr($digits, -4);

            return 'XXXX-XXXX-'.($last !== '' ? $last : 'XXXX');
        }

        if (strlen($number) <= 4) {
            return $number;
        }

        return str_repeat('X', strlen($number) - 4).substr($number, -4);
    }

    public static function statusLabel(?string $status, bool $submitted = true): string
    {
        if (!$submitted || $status === null || $status === '') {
            return 'Not Submitted';
        }

        return match ($status) {
            'pending' => 'Pending Verification',
            'approved', 'verified' => 'Verified',
            'rejected', 'failed' => 'Rejected',
            'expired' => 'Not Submitted',
            default => 'Pending Verification',
        };
    }
}
