<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TariffHistory extends Model
{
    protected $table = 'tariff_history';

    protected $fillable = [
        'tariff_id',
        'field_name',
        'old_value',
        'new_value',
        'changed_by',
        'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    /**
     * Тариф
     */
    public function tariff(): BelongsTo
    {
        return $this->belongsTo(Tariff::class);
    }

    /**
     * Пользователь, который изменил (админ)
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Скопы для удобной выборки
     */
    public function scopeForTariff($query, int $tariffId)
    {
        return $query->where('tariff_id', $tariffId);
    }

    public function scopeRecent($query, int $limit = 10)
    {
        return $query->orderBy('changed_at', 'desc')->limit($limit);
    }
}
