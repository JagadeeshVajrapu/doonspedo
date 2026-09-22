<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverSubscription extends Model
{
    protected $fillable = [
        'driver_id',
        'subscription_plan_id',
        'price',
        'starts_at',
        'expires_at',
        'status',
        'payment_proof',
        'admin_note',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function driver()
    {
        return $this->belongsTo(DriverRegistration::class, 'driver_id');
    }
}
