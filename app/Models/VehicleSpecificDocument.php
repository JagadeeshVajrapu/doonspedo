<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleSpecificDocument extends Model
{
    protected $fillable = [
        'vehicle_id',
        'document_name',
        'document_path',
        'status',
        'admin_message',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }
}
