<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverDocument extends Model
{
    //
    protected $fillable = ['driver_id', 'kyc_requirement_id', 'document_path', 'status'];

    public function driver()
    {
        return $this->belongsTo(DriverRegistration::class);
    }

    public function kycRequirement()
    {
        return $this->belongsTo(KycRequirement::class);
    }
}
