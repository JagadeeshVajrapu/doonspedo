<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id', 'driver_id', 'booking_id', 'type', 'amount',
        'balance_before', 'balance_after', 'description', 'reference_id',
        'reference_type', 'category', 'wallet_recharge_id', 'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'balance_before' => 'decimal:2',
            'balance_after' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function driver()
    {
        return $this->belongsTo(\App\Models\DriverRegistration::class, 'driver_id');
    }
}
