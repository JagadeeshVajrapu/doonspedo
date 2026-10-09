<?php

namespace App\Models;

use App\Support\DisplayNumber;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class DriverRegistration extends Model
{
    protected $fillable = [
        'display_no',
        'branch_id',
        'name',
        'mobile',
        'email',
        'vehicle_type',
        'vehicle_number',
        'min_price',
        'per_km_price',
        'license_number',
        'city',
        'status',
        'profile_image',
        'license_image',
        'aadhaar_image',
        'is_blocked',
        'commission_rate',
        'wallet_balance',
        'is_online',
        'current_lat',
        'current_lng',
        'working_hours',
        'ride_preferences',
        'locale',
        'currency_code',
        'theme',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'driver_id');
    }

    public function documents()
    {
        return $this->hasMany(DriverDocument::class, 'driver_id');
    }

    /**
     * Required, active KYC documents must all be approved before this driver
     * can receive or accept a ride. Account approval is checked separately.
     */
    public function rideDocumentsVerified(): bool
    {
        $requiredIds = KycRequirement::query()
            ->where('is_active', true)
            ->where('is_required', true)
            ->pluck('id');

        if ($requiredIds->isEmpty()) {
            return true;
        }

        $documents = $this->relationLoaded('documents')
            ? $this->documents
            : $this->documents()->get(['kyc_requirement_id', 'status']);

        $approved = $documents
            ->where('status', 'approved')
            ->pluck('kyc_requirement_id')
            ->unique()
            ->map(fn ($id) => (int) $id);

        return $requiredIds->map(fn ($id) => (int) $id)->diff($approved)->isEmpty();
    }

    public function documentVerificationLabel(): string
    {
        $requiredIds = KycRequirement::query()
            ->where('is_active', true)
            ->where('is_required', true)
            ->pluck('id');

        if ($requiredIds->isEmpty()) {
            return $this->status === 'rejected' ? 'Rejected' : ($this->status === 'approved' ? 'Verified' : 'Pending');
        }

        $documents = $this->relationLoaded('documents')
            ? $this->documents
            : $this->documents()->get(['kyc_requirement_id', 'status']);

        $rejected = false;
        $waiting = false;
        foreach ($requiredIds as $id) {
            $document = $documents->firstWhere('kyc_requirement_id', (int) $id)
                ?? $documents->firstWhere('kyc_requirement_id', $id);
            if (!$document || $document->status === 'pending') {
                $waiting = true;
            } elseif ($document->status === 'rejected') {
                $rejected = true;
            } elseif ($document->status !== 'approved') {
                $waiting = true;
            }
        }

        if (!$rejected && !$waiting) {
            return 'Verified';
        }

        return $rejected ? 'Rejected' : 'Pending';
    }

    public function canReceiveRides(): bool
    {
        return $this->status === 'approved'
            && !$this->is_blocked
            && $this->rideDocumentsVerified();
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'driver_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(DriverSubscription::class, 'driver_id');
    }

    public function activeSubscription()
    {
        return $this->hasOne(DriverSubscription::class, 'driver_id')->where('status', 'active')->where('expires_at', '>', now());
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'driver_id')->where('is_driver_review', false);
    }

    protected static function booted(): void
    {
        static::creating(function (DriverRegistration $driver) {
            if (!Schema::hasColumn($driver->getTable(), 'display_no') || $driver->display_no) {
                return;
            }

            $driver->display_no = ((int) static::query()->max('display_no')) + 1;
        });
    }

    public function displayReference(): string
    {
        return DisplayNumber::format((int) ($this->display_no ?: $this->id));
    }

    protected $casts = [
        'working_hours' => 'array',
        'ride_preferences' => 'array',
        'is_online' => 'boolean',
        'is_blocked' => 'boolean',
    ];
}
