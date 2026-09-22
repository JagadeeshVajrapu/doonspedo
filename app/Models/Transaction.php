<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id', 'driver_id', 'booking_id', 'type', 'amount', 'description', 'reference_id', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function driver()
    {
        return $this->belongsTo(\App\Models\DriverRegistration::class, 'driver_id');
    }
}
