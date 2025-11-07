<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $fillable = [
        'ticket_number',
        'user_id',
        'phone',
        'country_code',
        'subject',
        'message',
        'status',
        'closed_at',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    /**
     * Генерация уникального 10-значного номера тикета
     */
    public static function generateTicketNumber(): string
    {
        do {
            $number = str_pad(random_int(1000000000, 9999999999), 10, '0', STR_PAD_LEFT);
        } while (self::where('ticket_number', $number)->exists());

        return $number;
    }

    /**
     * Связь с пользователем
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Связь с сообщениями
     */
    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class);
    }

    /**
     * Проверка статусов
     */
    public function isNew(): bool
    {
        return $this->status === 'new';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    /**
     * Получить badge класс для статуса
     */
    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'new' => 'bg-info',
            'in_progress' => 'bg-warning',
            'closed' => 'bg-secondary',
            default => 'bg-secondary',
        };
    }

    /**
     * Получить название статуса на русском
     */
    public function getStatusName(): string
    {
        return match($this->status) {
            'new' => 'Новый',
            'in_progress' => 'В работе',
            'closed' => 'Закрыт',
            default => 'Неизвестно',
        };
    }
}
