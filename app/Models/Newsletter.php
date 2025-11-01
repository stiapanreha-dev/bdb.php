<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Newsletter extends Model
{
    protected $fillable = [
        'user_id',
        'is_active',
        'email',
        'last_sent_at',
        'subscription_ends_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_sent_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
    ];

    /**
     * Get the user that owns the newsletter.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the keywords for the newsletter.
     */
    public function keywords(): HasMany
    {
        return $this->hasMany(NewsletterKeyword::class);
    }

    /**
     * Get the logs for the newsletter.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(NewsletterLog::class);
    }

    /**
     * Get the email to send newsletters to.
     */
    public function getEmailAddress(): string
    {
        return $this->email ?? $this->user->email;
    }
}
