<?php

declare(strict_types=1);

namespace App\Modules\Tips\Providers;

use Illuminate\Support\ServiceProvider;

final class TipsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
