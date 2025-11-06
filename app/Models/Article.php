<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'images',
        'status',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'images' => 'array',
    ];

    /**
     * Связь с пользователем (автор статьи)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Проверка статуса
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Scope для активных статей
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
