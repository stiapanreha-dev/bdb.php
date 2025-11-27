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
        'work_email',
        'work_phone',
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

    /**
     * Get the newsletter for the user.
     */
    public function newsletter()
    {
        return $this->hasOne(Newsletter::class);
    }

    /**
     * Get the payments for the user.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the shopping cart for the user.
     */
    public function cart()
    {
        return $this->hasOne(ShopCart::class);
    }

    /**
     * Get or create the shopping cart for the user.
     */
    public function getOrCreateCart(): ShopCart
    {
        return $this->cart ?? ShopCart::create(['user_id' => $this->id]);
    }

    /**
     * Get the announcements for the user.
     */
    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    /**
     * Check if user has newsletter subscription access.
     */
    public function hasNewsletterAccess(): bool
    {
        // Admins always have access
        if ($this->isAdmin()) {
            return true;
        }

        // Users with active subscription have access
        return $this->hasActiveSubscription();
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\CustomResetPasswordNotification($token));
    }
}
