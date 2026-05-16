<?php

declare(strict_types=1);

namespace App\Modules\Vault\Providers;

use App\Modules\Vault\Commands\StoreHealthData\StoreHealthDataCommand;
use App\Modules\Vault\Commands\StoreHealthData\StoreHealthDataHandler;
use App\Modules\Vault\Queries\GetDecryptedHealthData\GetDecryptedHealthDataHandler;
use App\Modules\Vault\Queries\GetDecryptedHealthData\GetDecryptedHealthDataQuery;
use App\Modules\Vault\Queries\ListUserVaultEntries\ListUserVaultEntriesHandler;
use App\Modules\Vault\Queries\ListUserVaultEntries\ListUserVaultEntriesQuery;
use App\Shared\Bus\CommandBus;
use App\Shared\Bus\QueryBus;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

final class VaultServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->registerRoutes();
        $this->registerHandlers();
    }

    private function registerRoutes(): void
    {
        Route::prefix('api')
            ->middleware('api')
            ->group(__DIR__ . '/../Routes/api.php');
    }

    private function registerHandlers(): void
    {
        /** @var CommandBus $commandBus */
        $commandBus = $this->app->make(CommandBus::class);
        $commandBus->register(StoreHealthDataCommand::class, StoreHealthDataHandler::class);

        /** @var QueryBus $queryBus */
        $queryBus = $this->app->make(QueryBus::class);
        $queryBus->register(GetDecryptedHealthDataQuery::class, GetDecryptedHealthDataHandler::class);
        $queryBus->register(ListUserVaultEntriesQuery::class, ListUserVaultEntriesHandler::class);
    }
}
