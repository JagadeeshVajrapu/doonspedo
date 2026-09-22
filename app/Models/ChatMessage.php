<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'sender_id',
        'sender_type',
        'message',
        'is_read',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
