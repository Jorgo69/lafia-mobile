<?php

declare(strict_types=1);

namespace App\Modules\Sync\Jobs;

use App\Modules\Sync\Enums\SyncStatus;
use App\Modules\Sync\Services\SyncManager;
use App\Services\Settings\SettingsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

final class RunSyncJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public int $tries = 1;
    public int $timeout = 30;

    public function handle(SyncManager $syncManager, SettingsService $settings): void
    {
        $results = $syncManager->syncAll();
        $undoable = [];

        foreach ($results as $result) {
            Log::info("Sync [{$result->resource->value}]: {$result->status->value}" .
                ($result->itemsUpdated > 0 ? " ({$result->itemsUpdated} updated)" : ''));

            if ($result->status === SyncStatus::UPDATED && $result->canUndo && $result->itemsUpdated > 0) {
                $undoable[] = [
                    'resource' => $result->resource->value,
                    'items' => $result->itemsUpdated,
                ];
            }
        }

        if ($undoable !== []) {
            $settings->set('sync_undo_pending', json_encode([
                'updates' => $undoable,
                'at' => now()->toDateTimeString(),
            ], JSON_THROW_ON_ERROR));
        }
    }
}
