<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        'username',
        'email',
        'phone',
        'password',
        'balance',
        'role',
        'email_verified',
        'phone_verified',
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
            'balance' => 'decimal:2',
            'email_verified' => 'boolean',
            'phone_verified' => 'boolean',
        ];
    }

    /**
     * Get the transactions for the user.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the ideas for the user.
     */
    public function ideas()
    {
        return $this->hasMany(Idea::class);
    }

    /**
     * Get the email verifications for the user.
     */
    public function emailVerifications()
    {
        return $this->hasMany(EmailVerification::class);
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user has positive balance.
     */
    public function hasPositiveBalance(): bool
    {
        return $this->balance > 0;
    }

    /**
     * Get the subscriptions for the user.
     */
    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Get the active subscription for the user.
     */
    public function activeSubscription()
    {
        return $this->hasOne(UserSubscription::class)
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->latest('expires_at');
    }

    /**
     * Check if user has active subscription.
     */
    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }
}
