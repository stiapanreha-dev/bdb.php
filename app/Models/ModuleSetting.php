<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ModuleSetting extends Model
{
    protected $fillable = [
        'module_key',
        'module_name',
        'description',
        'is_enabled',
        'settings_route',
        'sort_order',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    /**
     * Scope для получения только активных модулей
     */
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    /**
     * Проверка включен ли модуль
     *
     * @param string $key
     * @return bool
     */
    public static function isModuleEnabled(string $key): bool
    {
        return Cache::remember("module_enabled_{$key}", 3600, function () use ($key) {
            $module = self::where('module_key', $key)->first();
            return $module ? $module->is_enabled : false;
        });
    }

    /**
     * Проверка наличия страницы настроек у модуля
     *
     * @return bool
     */
    public function hasSettings(): bool
    {
        return !empty($this->settings_route);
    }

    /**
     * Очистка кеша при изменении модуля
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($module) {
            Cache::forget("module_enabled_{$module->module_key}");
        });

        static::deleted(function ($module) {
            Cache::forget("module_enabled_{$module->module_key}");
        });
    }
}
