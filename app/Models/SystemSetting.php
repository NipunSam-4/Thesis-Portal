<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    use HasFactory;

    protected $table = 'system_settings';

    protected $fillable = [
        'key',
        'value',
        'label',
        'description',
    ];

    /**
     * Get a setting value by key with caching and default fallback.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            return Cache::rememberForever("sys_setting_{$key}", function () use ($key, $default) {
                $setting = self::where('key', $key)->first();
                return $setting !== null && $setting->value !== null ? $setting->value : $default;
            });
        } catch (\Throwable $e) {
            return $default;
        }
    }

    /**
     * Set a setting value and invalidate its cache entry.
     */
    public static function set(string $key, mixed $value, ?string $label = null, ?string $description = null): self
    {
        $payload = ['value' => (string)$value];
        if ($label !== null) {
            $payload['label'] = $label;
        }
        if ($description !== null) {
            $payload['description'] = $description;
        }

        $setting = self::updateOrCreate(['key' => $key], $payload);
        Cache::forget("sys_setting_{$key}");

        return $setting;
    }

    /**
     * Clear the cache for a given setting or all settings.
     */
    public static function clearCache(?string $key = null): void
    {
        if ($key) {
            Cache::forget("sys_setting_{$key}");
        } else {
            foreach (self::pluck('key') as $k) {
                Cache::forget("sys_setting_{$k}");
            }
        }
    }
}
