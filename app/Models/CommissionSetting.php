<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionSetting extends Model
{
    protected $fillable = [
        'type',
        'amount',
        'is_active',
        'low_balance_threshold',
        'min_recharge',
        'max_recharge',
        'effective_from',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'is_active' => 'boolean',
            'low_balance_threshold' => 'decimal:2',
            'min_recharge' => 'decimal:2',
            'max_recharge' => 'decimal:2',
            'effective_from' => 'datetime',
        ];
    }

    public static function current(): ?self
    {
        return static::query()->latest('id')->first();
    }

    public function label(): string
    {
        $amount = number_format((float) $this->amount, 2);

        if ($this->type === 'percentage') {
            return $amount.'% of the completed fare';
        }

        return '₹'.$amount.' per completed ride';
    }
}
