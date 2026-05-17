<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

final class EnsureOnboarded
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('welcome') || $request->is('livewire/*')) {
            return $next($request);
        }

        if (! $this->isOnboarded()) {
            return redirect('/welcome');
        }

        return $next($request);
    }

    private function isOnboarded(): bool
    {
        try {
            $setting = DB::table('settings')->where('key', 'onboarding_done')->first();
            return $setting?->value === '1';
        } catch (\Throwable) {
            return false;
        }
    }
}
