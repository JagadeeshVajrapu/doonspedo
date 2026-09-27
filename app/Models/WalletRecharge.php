<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletRecharge extends Model
{
    protected $fillable = [
        'driver_id',
        'amount',
        'payment_method',
        'payment_qr_code_id',
        'payment_reference',
        'status',
        'verified_by',
        'verified_at',
        'submitted_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'verified_at' => 'datetime',
            'submitted_at' => 'datetime',
        ];
    }

    public function driver()
    {
        return $this->belongsTo(DriverRegistration::class, 'driver_id');
    }

    public function qrCode()
    {
        return $this->belongsTo(PaymentQrCode::class, 'payment_qr_code_id');
    }

    public function verifier()
    {
        return $this->belongsTo(Admin::class, 'verified_by');
    }
}
