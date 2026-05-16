<?php

declare(strict_types=1);

namespace App\Modules\Ussd\Providers;

use Illuminate\Support\ServiceProvider;

final class UssdServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
