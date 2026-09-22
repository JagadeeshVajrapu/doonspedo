<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'name',
        'price',
        'duration_days',
        'max_rides',
        'max_ride_amount',
        'is_active',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
