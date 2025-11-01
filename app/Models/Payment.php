<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'yookassa_payment_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'description',
        'metadata',
        'paid_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    /**
     * Связь с пользователем
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Проверка успешности платежа
     */
    public function isSucceeded(): bool
    {
        return $this->status === 'succeeded';
    }

    /**
     * Проверка ожидания платежа
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Проверка отмены платежа
     */
    public function isCanceled(): bool
    {
        return $this->status === 'canceled';
    }
}
