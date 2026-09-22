<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'title',
        'message',
        'type',
        'action_url',
        'is_read',
    ];

    public function driver()
    {
        return $this->belongsTo(DriverRegistration::class, 'driver_id');
    }
}
