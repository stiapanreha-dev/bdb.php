<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsletterLog extends Model
{
    protected $fillable = [
        'newsletter_id',
        'sent_at',
        'zakupki_count',
        'status',
        'error_message',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'zakupki_count' => 'integer',
    ];

    /**
     * Get the newsletter that owns the log.
     */
    public function newsletter(): BelongsTo
    {
        return $this->belongsTo(Newsletter::class);
    }
}
