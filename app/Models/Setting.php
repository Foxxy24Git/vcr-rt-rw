<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    private const CACHE_KEY = 'settings';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Ambil nilai setting dari cache (atau dari DB lalu di-cache selamanya).
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = self::allCached();

        return array_key_exists($key, $settings) ? $settings[$key] : $default;
    }

    /**
     * Set nilai setting: update DB lalu bersihkan cache agar get() baca ulang dari DB.
     */
    public static function set(string $key, mixed $value): void
    {
        self::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value === null ? null : (string) $value]
        );

        self::clearCache();
    }

    /**
     * Ambil semua setting dari cache. Jika belum di-cache, muat dari DB dan cache selamanya.
     *
     * @return array<string, string|null>
     */
    public static function allCached(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function (): array {
            return self::query()
                ->pluck('value', 'key')
                ->all();
        });
    }

    /**
     * Hapus cache settings (dipanggil setelah set()).
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
