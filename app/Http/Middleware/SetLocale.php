<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

final class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);
        App::setLocale($locale);

        return $next($request);
    }

    private function resolveLocale(Request $request): string
    {
        $available = config('app.available_locales', ['fr']);

        // 1. Stored preference in SQLite (highest priority — user chose this)
        $stored = $this->getStoredLocale();
        if ($stored !== null && in_array($stored, $available, true)) {
            return $stored;
        }

        // 2. Detect from browser/phone Accept-Language header
        $detected = $request->getPreferredLanguage($available);
        if ($detected !== null) {
            return $detected;
        }

        // 3. Default: FR
        return config('app.locale', 'fr');
    }

    private function getStoredLocale(): ?string
    {
        try {
            $setting = DB::table('settings')->where('key', 'locale')->first();
            return $setting?->value;
        } catch (\Throwable) {
            return null;
        }
    }
}
