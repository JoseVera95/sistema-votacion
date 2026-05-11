<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    // OBTENER CONFIG CON CACHE
    public static function get($key, $default = null)
    {
        return Cache::remember("setting_$key", 3600, function () use ($key, $default) {
            return self::where('key', $key)->value('value') ?? $default;
        });
    }

    // GUARDAR CONFIG
    public static function set($key, $value)
{
    Cache::forget("setting_$key");

    return self::updateOrCreate(
        ['key' => $key],
        ['value' => $value === null ? '' : $value]
    );
}

    // LIMPIAR TODO EL CACHE (opcional)
    public static function clear()
    {
        Cache::flush();
    }
}
