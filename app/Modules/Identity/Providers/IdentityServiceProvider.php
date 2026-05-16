<?php

declare(strict_types=1);

namespace App\Modules\Identity\Providers;

use App\Modules\Identity\Commands\AddGuardian\AddGuardianCommand;
use App\Modules\Identity\Commands\AddGuardian\AddGuardianHandler;
use App\Modules\Identity\Commands\ApproveRecovery\ApproveRecoveryCommand;
use App\Modules\Identity\Commands\ApproveRecovery\ApproveRecoveryHandler;
use App\Modules\Identity\Commands\RegisterDevice\RegisterDeviceCommand;
use App\Modules\Identity\Commands\RegisterDevice\RegisterDeviceHandler;
use App\Modules\Identity\Commands\RequestRecovery\RequestRecoveryCommand;
use App\Modules\Identity\Commands\RequestRecovery\RequestRecoveryHandler;
use App\Modules\Identity\Queries\GetIdentity\GetIdentityHandler;
use App\Modules\Identity\Queries\GetIdentity\GetIdentityQuery;
use App\Modules\Identity\Queries\GetRecoveryStatus\GetRecoveryStatusHandler;
use App\Modules\Identity\Queries\GetRecoveryStatus\GetRecoveryStatusQuery;
use App\Modules\Identity\Services\ShamirSecretSharingService;
use App\Shared\Bus\CommandBus;
use App\Shared\Bus\QueryBus;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

final class IdentityServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ShamirSecretSharingService::class);
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
        $commandBus->register(RegisterDeviceCommand::class, RegisterDeviceHandler::class);
        $commandBus->register(AddGuardianCommand::class, AddGuardianHandler::class);
        $commandBus->register(RequestRecoveryCommand::class, RequestRecoveryHandler::class);
        $commandBus->register(ApproveRecoveryCommand::class, ApproveRecoveryHandler::class);

        /** @var QueryBus $queryBus */
        $queryBus = $this->app->make(QueryBus::class);
        $queryBus->register(GetIdentityQuery::class, GetIdentityHandler::class);
        $queryBus->register(GetRecoveryStatusQuery::class, GetRecoveryStatusHandler::class);
    }
}
