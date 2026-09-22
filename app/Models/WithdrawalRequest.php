<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawalRequest extends Model
{
    protected $fillable = [
        'driver_id',
        'amount',
        'payment_method',
        'payment_details',
        'status',
        'admin_note',
        'processed_at',
    ];

    public function driver()
    {
        return $this->belongsTo(DriverRegistration::class, 'driver_id');
    }
}
