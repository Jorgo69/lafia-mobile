<?php

declare(strict_types=1);

namespace App\Modules\Sync\Services;

use App\Modules\Sync\Enums\SyncResource;
use App\Modules\Sync\Models\SyncSnapshot;
use App\Modules\Sync\Models\SyncVersion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class SyncRollbackService
{
    private const float MIN_COUNT_RATIO = 0.5;

    /** @var array<string, SyncHandlerInterface> */
    private array $handlers = [];

    public function registerHandler(SyncResource $resource, SyncHandlerInterface $handler): void
    {
        $this->handlers[$resource->value] = $handler;
    }

    /**
     * Take a snapshot of current data before applying new sync.
     */
    public function takeSnapshot(SyncResource $resource): void
    {
        $handler = $this->handlers[$resource->value] ?? null;

        if ($handler === null) {
            return;
        }

        $data = $handler->snapshot();
        $syncVersion = SyncVersion::find($resource->value);

        SyncSnapshot::updateOrCreate(
            ['resource' => $resource->value],
            [
                'version' => $syncVersion?->version ?? '0',
                'data' => $data,
                'item_count' => $this->countItems($resource, $data),
                'created_at' => now(),
            ],
        );
    }

    /**
     * Validate that new data is sane (not empty, not suspiciously small).
     *
     * @param array<mixed> $newData
     */
    public function validateNewData(SyncResource $resource, array $newData): bool
    {
        $snapshot = SyncSnapshot::find($resource->value);

        if ($snapshot === null) {
            return true;
        }

        $oldCount = $snapshot->item_count;
        $newCount = $this->countItems($resource, $newData);

        if ($oldCount === 0) {
            return true;
        }

        if ($newCount === 0) {
            Log::warning("Sync rejected for {$resource->value}: received empty dataset (had {$oldCount} items).");
            return false;
        }

        $ratio = $newCount / $oldCount;

        if ($ratio < self::MIN_COUNT_RATIO) {
            Log::warning("Sync rejected for {$resource->value}: suspicious drop {$oldCount} -> {$newCount} (ratio: {$ratio}).");
            return false;
        }

        return true;
    }

    /**
     * Restore data from the snapshot (user-triggered or auto).
     */
    public function rollback(SyncResource $resource): int
    {
        $handler = $this->handlers[$resource->value] ?? null;
        $snapshot = SyncSnapshot::find($resource->value);

        if ($handler === null || $snapshot === null) {
            return 0;
        }

        $restored = 0;

        try {
            DB::transaction(function () use ($handler, $snapshot, $resource, &$restored) {
                $restored = $handler->restore($snapshot->data);

                SyncVersion::where('resource', $resource->value)->update([
                    'version' => $snapshot->version,
                ]);
            });

            Log::info("Rollback completed for {$resource->value}: {$restored} items restored to version {$snapshot->version}.");
        } catch (\Throwable $e) {
            Log::error("Rollback failed for {$resource->value}: {$e->getMessage()}");
            return 0;
        }

        return $restored;
    }

    public function hasSnapshot(SyncResource $resource): bool
    {
        return SyncSnapshot::where('resource', $resource->value)->exists();
    }

    /**
     * @return array{version: string, item_count: int, created_at: string}|null
     */
    public function getSnapshotInfo(SyncResource $resource): ?array
    {
        $snapshot = SyncSnapshot::find($resource->value);

        if ($snapshot === null) {
            return null;
        }

        return [
            'version' => $snapshot->version,
            'item_count' => $snapshot->item_count,
            'created_at' => $snapshot->created_at->toDateTimeString(),
        ];
    }

    /**
     * @param array<mixed> $data
     */
    private function countItems(SyncResource $resource, array $data): int
    {
        if ($resource === SyncResource::HEALTH_SCHEMA) {
            return count($data['fields'] ?? []);
        }

        return count($data);
    }
}
