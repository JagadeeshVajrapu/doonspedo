<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'driver_id',
        'vehicle_category_id',
        'service_type',
        'status',
        'ride_otp',
        'ride_otp_verified_at',
        'pickup_location',
        'dropoff_location',
        'distance',
        'fare',
        'commission_amount',
        'net_amount',
        'payment_method',
        'payment_status',
        'notes',
        'parcel_details',
        'pickup_lat',
        'pickup_lng',
        'dropoff_lat',
        'dropoff_lng',
        'accepted_at',
        'picked_up_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $hidden = [
        'ride_otp',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'ride_otp_verified_at' => 'datetime',
    ];

    public static function generateRideOtp(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function driver()
    {
        return $this->belongsTo(DriverRegistration::class, 'driver_id');
    }

    public function vehicleCategory()
    {
        return $this->belongsTo(VehicleCategory::class);
    }

    public function bids()
    {
        return $this->hasMany(Bid::class);
    }
}
