<?php

declare(strict_types=1);

namespace App\Shared\Bus;

use Illuminate\Support\ServiceProvider;

final class BusServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CommandBus::class);
        $this->app->singleton(QueryBus::class);
    }
}
