<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'driver_id',
        'vehicle_category_id',
        'brand',
        'model',
        'number_plate',
        'color',
        'year',
        'status',
        'verification_status',
        'min_price',
        'per_km_price',
        'admin_message',
    ];

    public function driver()
    {
        return $this->belongsTo(DriverRegistration::class, 'driver_id');
    }

    public function category()
    {
        return $this->belongsTo(VehicleCategory::class, 'vehicle_category_id');
    }

    public function documents()
    {
        return $this->hasMany(VehicleSpecificDocument::class, 'vehicle_id');
    }
}
