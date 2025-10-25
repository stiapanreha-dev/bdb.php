<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tariff extends Model
{
    protected $fillable = [
        'name',
        'duration_days',
        'price',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_days' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Подписки пользователей на этот тариф
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * История изменений тарифа
     */
    public function history(): HasMany
    {
        return $this->hasMany(TariffHistory::class);
    }

    /**
     * Записать изменение в историю
     */
    public function logChange(string $fieldName, $oldValue, $newValue, ?int $changedBy = null): void
    {
        $this->history()->create([
            'field_name' => $fieldName,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'changed_by' => $changedBy,
            'changed_at' => now(),
        ]);
    }

    /**
     * Скопы для удобной выборки
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWeekly($query)
    {
        return $query->where('duration_days', 7);
    }

    public function scopeMonthly($query)
    {
        return $query->where('duration_days', 30);
    }
}
