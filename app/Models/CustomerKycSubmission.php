<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerKycSubmission extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'document_type',
        'document_label',
        'document_number',
        'document_path',
        'status',
        'rejection_reason',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
