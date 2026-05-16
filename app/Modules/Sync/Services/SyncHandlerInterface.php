<?php

declare(strict_types=1);

namespace App\Modules\Sync\Services;

interface SyncHandlerInterface
{
    /**
     * Apply remote data to the local database.
     *
     * @param array<mixed> $data The diff/full data from the remote server
     * @return int Number of items updated
     */
    public function apply(array $data): int;

    /**
     * Extract current local data as a restorable snapshot.
     *
     * @return array<mixed>
     */
    public function snapshot(): array;

    /**
     * Restore local data from a snapshot, replacing current data.
     *
     * @param array<mixed> $data The snapshot data
     * @return int Number of items restored
     */
    public function restore(array $data): int;
}
