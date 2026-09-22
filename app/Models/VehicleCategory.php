<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleCategory extends Model
{
    protected $fillable = [
        'branch_id',
        'name',
        'description',
        'icon',
        'capacity_seats',
        'capacity_bags',
        'base_fare',
        'rate_per_km',
        'is_active',
    ];
}

