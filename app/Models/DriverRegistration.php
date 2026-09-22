<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverRegistration extends Model
{
    protected $fillable = [
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

    protected $casts = [
        'working_hours' => 'array',
        'ride_preferences' => 'array',
        'is_online' => 'boolean',
        'is_blocked' => 'boolean',
    ];
}
