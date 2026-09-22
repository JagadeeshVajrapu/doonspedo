<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'driver_id',
        'ticket_number',
        'subject',
        'description',
        'category',
        'priority',
        'status',
    ];

    public function messages()
    {
        return $this->hasMany(SupportMessage::class);
    }

    public function driver()
    {
        return $this->belongsTo(DriverRegistration::class, 'driver_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
