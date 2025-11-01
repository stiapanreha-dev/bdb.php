<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsletterKeyword extends Model
{
    protected $fillable = [
        'newsletter_id',
        'keywords',
    ];

    /**
     * Get the newsletter that owns the keyword.
     */
    public function newsletter(): BelongsTo
    {
        return $this->belongsTo(Newsletter::class);
    }

    /**
     * Get keywords as array.
     */
    public function getKeywordsArray(): array
    {
        // Split by comma or space
        $keywords = preg_split('/[,\s]+/', trim($this->keywords), -1, PREG_SPLIT_NO_EMPTY);
        return array_filter($keywords);
    }
}
