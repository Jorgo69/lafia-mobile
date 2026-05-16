<?php

declare(strict_types=1);

namespace App\Modules\Emergency\Providers;

use App\Modules\Emergency\Commands\ReportServiceUpdate\ReportServiceUpdateCommand;
use App\Modules\Emergency\Commands\ReportServiceUpdate\ReportServiceUpdateHandler;
use App\Modules\Emergency\Queries\GetAllCenters\GetAllCentersHandler;
use App\Modules\Emergency\Queries\GetAllCenters\GetAllCentersQuery;
use App\Modules\Emergency\Queries\GetContactsByOperator\GetContactsByOperatorHandler;
use App\Modules\Emergency\Queries\GetContactsByOperator\GetContactsByOperatorQuery;
use App\Modules\Emergency\Queries\GetNearestCenter\GetNearestCenterHandler;
use App\Modules\Emergency\Queries\GetNearestCenter\GetNearestCenterQuery;
use App\Modules\Emergency\Services\OperatorDetectorService;
use App\Shared\Bus\CommandBus;
use App\Shared\Bus\QueryBus;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

final class EmergencyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(OperatorDetectorService::class);
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
        /** @var QueryBus $queryBus */
        $queryBus = $this->app->make(QueryBus::class);
        $queryBus->register(GetAllCentersQuery::class, GetAllCentersHandler::class);
        $queryBus->register(GetContactsByOperatorQuery::class, GetContactsByOperatorHandler::class);
        $queryBus->register(GetNearestCenterQuery::class, GetNearestCenterHandler::class);

        /** @var CommandBus $commandBus */
        $commandBus = $this->app->make(CommandBus::class);
        $commandBus->register(ReportServiceUpdateCommand::class, ReportServiceUpdateHandler::class);
    }
}
