<?php

declare(strict_types=1);

namespace App\Modules\Pharmacy\Providers;

use Illuminate\Support\ServiceProvider;

final class PharmacyServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
