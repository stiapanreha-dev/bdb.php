<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EmailVerification extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'code',
        'verification_type',
        'expires_at',
        'is_used',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'is_used' => 'boolean',
        ];
    }

    /**
     * Get the user that owns the verification.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a random 6-digit verification code.
     */
    public static function generateCode(int $length = 6): string
    {
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= random_int(0, 9);
        }
        return $code;
    }

    /**
     * Check if the verification code is expired.
     */
    public function isExpired(): bool
    {
        return Carbon::now()->greaterThan($this->expires_at);
    }

    /**
     * Check if the verification code is valid.
     */
    public function isValid(string $code): bool
    {
        return !$this->isExpired() && !$this->is_used && $this->code === $code;
    }
}
