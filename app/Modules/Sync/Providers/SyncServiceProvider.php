<?php

declare(strict_types=1);

namespace App\Modules\Sync\Providers;

use App\Modules\Sync\Enums\SyncResource;
use App\Modules\Sync\Handlers\EmergencyContactsSyncHandler;
use App\Modules\Sync\Handlers\HealthSchemaSyncHandler;
use App\Modules\Sync\Handlers\PharmaciesSyncHandler;
use App\Modules\Sync\Handlers\PracticalTipsSyncHandler;
use App\Modules\Sync\Handlers\ShortCodesSyncHandler;
use App\Modules\Sync\Services\DynamicSchemaService;
use App\Modules\Sync\Services\RemoteConfigService;
use App\Modules\Sync\Services\SyncManager;
use App\Modules\Sync\Services\SyncRollbackService;
use Illuminate\Support\ServiceProvider;

final class SyncServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(RemoteConfigService::class, function () {
            return new RemoteConfigService(
                baseUrl: config('services.lafia_sync.url', 'https://api.lafia.bj'),
            );
        });

        $this->app->singleton(DynamicSchemaService::class);
        $this->app->singleton(SyncRollbackService::class);

        $this->app->singleton(SyncManager::class, function ($app) {
            $manager = new SyncManager(
                $app->make(RemoteConfigService::class),
                $app->make(SyncRollbackService::class),
            );

            $manager->registerHandler(
                SyncResource::EMERGENCY_CONTACTS,
                $app->make(EmergencyContactsSyncHandler::class),
            );

            $manager->registerHandler(
                SyncResource::SHORT_CODES,
                $app->make(ShortCodesSyncHandler::class),
            );

            $manager->registerHandler(
                SyncResource::HEALTH_SCHEMA,
                $app->make(HealthSchemaSyncHandler::class),
            );

            $manager->registerHandler(
                SyncResource::PRACTICAL_TIPS,
                $app->make(PracticalTipsSyncHandler::class),
            );

            $manager->registerHandler(
                SyncResource::PHARMACIES,
                $app->make(PharmaciesSyncHandler::class),
            );

            return $manager;
        });
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
