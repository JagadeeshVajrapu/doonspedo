<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerAadhaarVerification extends Model
{
    protected $fillable = [
        'user_id',
        'aadhaar_hash',
        'aadhaar_last4',
        'provider_reference',
        'status',
        'attempts',
        'expires_at',
        'verified_at',
    ];

    protected $hidden = [
        'aadhaar_hash',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'attempts' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
