<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'discount_type', 'discount_value', 'min_booking_amount',
        'max_discount_amount', 'valid_from', 'valid_until', 'usage_limit',
        'used_count', 'is_active'
    ];
}
