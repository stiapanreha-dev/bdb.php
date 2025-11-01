<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class NewsletterSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    /**
     * Получить значение настройки с кэшированием
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("newsletter_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();

            if (!$setting) {
                return $default;
            }

            return self::castValue($setting->value, $setting->type);
        });
    }

    /**
     * Установить значение настройки
     */
    public static function set(string $key, mixed $value): void
    {
        $setting = self::where('key', $key)->first();

        if ($setting) {
            $setting->update(['value' => (string) $value]);
        } else {
            self::create([
                'key' => $key,
                'value' => (string) $value,
                'type' => self::detectType($value),
            ]);
        }

        Cache::forget("newsletter_setting_{$key}");
    }

    /**
     * Приведение значения к правильному типу
     */
    protected static function castValue(string $value, string $type): mixed
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'time', 'string' => $value,
            default => $value,
        };
    }

    /**
     * Определение типа значения
     */
    protected static function detectType(mixed $value): string
    {
        if (is_bool($value)) {
            return 'boolean';
        }
        if (is_int($value)) {
            return 'integer';
        }
        return 'string';
    }

    /**
     * Получить все настройки как массив
     */
    public static function getAll(): array
    {
        return self::all()->mapWithKeys(function ($setting) {
            return [$setting->key => self::castValue($setting->value, $setting->type)];
        })->toArray();
    }
}
