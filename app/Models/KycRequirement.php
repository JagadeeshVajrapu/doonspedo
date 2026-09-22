<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KycRequirement extends Model
{
    //
    protected $fillable = ['document_name', 'document_type', 'is_required', 'is_active'];

    public function driverDocuments()
    {
        return $this->hasMany(DriverDocument::class);
    }
}
