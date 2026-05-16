<?php

declare(strict_types=1);

namespace App\Services\Settings;

use Illuminate\Support\Facades\DB;

final class SettingsService
{
    /** @var array<string, ?string> */
    private static array $cache = [];

    public function get(string $key, ?string $default = null): ?string
    {
        if (array_key_exists($key, self::$cache)) {
            return self::$cache[$key] ?? $default;
        }

        $row = DB::table('settings')->where('key', $key)->first();
        $value = $row?->value;

        self::$cache[$key] = $value;

        return $value ?? $default;
    }

    public function set(string $key, ?string $value): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => $key],
            ['value' => $value],
        );

        self::$cache[$key] = $value;
    }

    public static function clearCache(): void
    {
        self::$cache = [];
    }

    public function isDarkMode(): bool
    {
        $val = $this->get('dark_mode', '0');
        return $val === '1' || $val === 'true';
    }

    // --- Auth session (system lock) ---

    public function isLockEnabled(): bool
    {
        return $this->get('lock_enabled', '0') === '1';
    }

    /**
     * Lock interval in minutes (default 1).
     */
    public function lockInterval(): int
    {
        return (int) ($this->get('lock_interval', '1') ?? '1');
    }

    public function isPinSessionActive(): bool
    {
        $expiresAt = $this->get('auth_session_expires');

        if ($expiresAt === null) {
            return false;
        }

        return now()->timestamp < (int) $expiresAt;
    }

    public function refreshPinSession(): void
    {
        $interval = $this->lockInterval();
        $this->set('auth_session_expires', (string) now()->addMinutes($interval)->timestamp);
    }
}
