<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeSetting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Ambil semua setting sebagai key-value array.
     * Contoh: HomeSetting::all()->toArray() → ['hero_title' => '...', ...]
     */
    public static function getAllKeyed(): array
    {
        return static::all()->pluck('value', 'key')->toArray();
    }

    /**
     * Ambil satu nilai berdasarkan key, dengan default fallback.
     */
    public static function get(string $key, string $default = ''): string
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    /**
     * Set satu nilai.
     */
    public static function set(string $key, string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
