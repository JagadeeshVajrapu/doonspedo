<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Support\DisplayNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'photo',
        'wallet_balance',
        'google_id',
        'facebook_id',
        'display_no',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (!Schema::hasColumn($user->getTable(), 'display_no') || $user->display_no) {
                return;
            }

            $user->display_no = ((int) static::query()->max('display_no')) + 1;
        });
    }

    public function displayReference(): string
    {
        return DisplayNumber::format((int) ($this->display_no ?: $this->id));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
