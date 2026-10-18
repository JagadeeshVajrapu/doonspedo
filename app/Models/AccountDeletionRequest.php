<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AccountDeletionRequest extends Model
{
    public const STATUS_PENDING_VERIFICATION = 'pending_verification';
    public const STATUS_PENDING_REVIEW = 'pending_review';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'reference_no',
        'account_type',
        'account_id',
        'email',
        'mobile',
        'status',
        'verification_channel',
        'is_verified',
        'verified_at',
        'verification_code_hash',
        'otp_expires_at',
        'otp_attempts',
        'admin_notes',
        'rejection_reason',
        'processed_by',
        'processed_at',
        'completed_at',
        'ip_address',
        'user_agent',
    ];

    protected $hidden = [
        'verification_code_hash',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'otp_expires_at' => 'datetime',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public static function makeReference(): string
    {
        do {
            $reference = 'ADR-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
        } while (static::query()->where('reference_no', $reference)->exists());

        return $reference;
    }

    public function isOpen(): bool
    {
        return !in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_REJECTED], true);
    }
}
