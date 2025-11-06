<?php

use App\Models\ModuleSetting;
use Illuminate\Support\Facades\Cache;

if (!function_exists('module_enabled')) {
    /**
     * Проверка включен ли модуль
     * Использует кеширование на 1 час для оптимизации
     *
     * @param string $key Ключ модуля
     * @return bool
     */
    function module_enabled(string $key): bool
    {
        return Cache::remember("module_enabled_{$key}", 3600, function () use ($key) {
            $module = ModuleSetting::where('module_key', $key)->first();
            return $module ? $module->is_enabled : false;
        });
    }
}
