<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'category',
        'title',
        'description',
        'images',
        'register_as_purchase',
        'company_id',
        'status',
        'published_at',
    ];

    protected $casts = [
        'register_as_purchase' => 'boolean',
        'published_at' => 'datetime',
        'images' => 'array',
    ];

    // Связь с пользователем (автор объявления)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Связь с компанией пользователя
    public function company()
    {
        return $this->belongsTo(User::class, 'company_id');
    }

    // Проверка типа объявления
    public function isSupplier()
    {
        return $this->type === 'supplier';
    }

    public function isBuyer()
    {
        return $this->type === 'buyer';
    }

    public function isDealer()
    {
        return $this->type === 'dealer';
    }

    // Проверка статуса
    public function isActive()
    {
        return $this->status === 'active';
    }

    // Scope для активных объявлений
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope по типу
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}
