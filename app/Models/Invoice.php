<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'booking_id', 'invoice_number', 'amount', 'tax_amount', 'total_amount', 'status', 'due_date'
    ];

    public function booking()
    {
        return $this->belongsTo(\App\Models\Booking::class);
    }
}
