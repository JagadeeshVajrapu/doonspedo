<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentQrCode extends Model
{
    protected $fillable = [
        'title',
        'upi_id',
        'image_path',
        'is_active',
        'is_test',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_test' => 'boolean',
        ];
    }

    public function imageUrl(): string
    {
        return media_url($this->image_path);
    }

    public static function active(): ?self
    {
        return static::query()->where('is_active', true)->latest('id')->first();
    }
}
