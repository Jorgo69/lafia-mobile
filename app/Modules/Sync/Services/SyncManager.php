<?php

declare(strict_types=1);

namespace App\Modules\Sync\Services;

use App\Modules\Sync\DTOs\SyncResult;
use App\Modules\Sync\Enums\SyncResource;
use App\Modules\Sync\Enums\SyncStatus;
use App\Modules\Sync\Models\SyncVersion;
use Illuminate\Support\Facades\Log;

final class SyncManager
{
    /** @var array<string, SyncHandlerInterface> */
    private array $handlers = [];

    public function __construct(
        private readonly RemoteConfigService $remoteConfig,
        private readonly SyncRollbackService $rollbackService,
    ) {}

    public function registerHandler(SyncResource $resource, SyncHandlerInterface $handler): void
    {
        $this->handlers[$resource->value] = $handler;
        $this->rollbackService->registerHandler($resource, $handler);
    }

    /**
     * Run sync for all due resources, ordered by priority.
     *
     * @return array<SyncResult>
     */
    public function syncAll(): array
    {
        $results = [];
        $resources = SyncResource::cases();

        usort($resources, fn (SyncResource $a, SyncResource $b) => $b->priority() - $a->priority());

        foreach ($resources as $resource) {
            if (!$this->isDue($resource)) {
                continue;
            }

            if (!isset($this->handlers[$resource->value])) {
                continue;
            }

            $results[] = $this->syncResource($resource);
        }

        return $results;
    }

    public function syncResource(SyncResource $resource): SyncResult
    {
        $handler = $this->handlers[$resource->value] ?? null;

        if ($handler === null) {
            return new SyncResult($resource, SyncStatus::FAILED, error: 'No handler registered');
        }

        $check = $this->remoteConfig->check($resource);

        if ($check['status'] === SyncStatus::OFFLINE) {
            Log::info("Sync offline for {$resource->value}, using local data.");
            return new SyncResult($resource, SyncStatus::OFFLINE);
        }

        if ($check['status'] === SyncStatus::UP_TO_DATE) {
            $this->remoteConfig->markSynced($resource, $check['version'] ?? '0', $check['etag']);
            return new SyncResult($resource, SyncStatus::UP_TO_DATE, $check['version']);
        }

        if ($check['status'] === SyncStatus::UPDATED && $check['data'] !== null) {
            // Validate before applying: reject suspiciously small datasets
            if (!$this->rollbackService->validateNewData($resource, $check['data'])) {
                return new SyncResult($resource, SyncStatus::FAILED, error: 'Data rejected: suspicious content drop');
            }

            // Snapshot current data before overwriting
            $previousVersion = SyncVersion::find($resource->value)?->version;
            $this->rollbackService->takeSnapshot($resource);

            try {
                $itemsUpdated = $handler->apply($check['data']);
            } catch (\Throwable $e) {
                Log::error("Sync apply failed for {$resource->value}: {$e->getMessage()}");
                return new SyncResult($resource, SyncStatus::FAILED, error: 'Apply failed: ' . $e->getMessage());
            }

            $this->remoteConfig->markSynced(
                $resource,
                $check['version'] ?? '0',
                $check['etag'],
            );

            Log::info("Sync completed for {$resource->value}: {$itemsUpdated} items updated.");

            return new SyncResult(
                resource: $resource,
                status: SyncStatus::UPDATED,
                newVersion: $check['version'],
                itemsUpdated: $itemsUpdated,
                canUndo: $previousVersion !== null,
                previousVersion: $previousVersion,
            );
        }

        return new SyncResult($resource, SyncStatus::FAILED, error: 'Unexpected response');
    }

    public function rollback(SyncResource $resource): int
    {
        return $this->rollbackService->rollback($resource);
    }

    private function isDue(SyncResource $resource): bool
    {
        $syncVersion = SyncVersion::find($resource->value);

        return $syncVersion === null || $syncVersion->isDue();
    }
}
