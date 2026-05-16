<?php

declare(strict_types=1);

namespace App\Modules\Sync\Services;

use App\Modules\Sync\DTOs\SyncResult;
use App\Modules\Sync\Enums\SyncResource;
use App\Modules\Sync\Enums\SyncStatus;
use App\Modules\Sync\Models\SyncVersion;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class RemoteConfigService
{
    public function __construct(
        private readonly string $baseUrl,
    ) {}

    /**
     * Ping the remote for a resource update.
     * Returns null payload if 304 Not Modified, or the diff data if updated.
     *
     * @return array{status: SyncStatus, version: ?string, etag: ?string, data: ?array<mixed>}
     */
    public function check(SyncResource $resource): array
    {
        $syncVersion = SyncVersion::find($resource->value);
        $currentEtag = $syncVersion?->etag;

        try {
            $response = Http::timeout(10)
                ->withHeaders(array_filter([
                    'If-None-Match' => $currentEtag,
                    'X-Lafia-Version' => $syncVersion?->version ?? '0',
                ]))
                ->get("{$this->baseUrl}/api/sync/{$resource->value}");

            if ($response->status() === 304) {
                return [
                    'status' => SyncStatus::UP_TO_DATE,
                    'version' => $syncVersion?->version,
                    'etag' => $currentEtag,
                    'data' => null,
                ];
            }

            if ($response->successful()) {
                $body = $response->json();

                // Support both flat format and nested {success, data: {version, data}}
                $payload = $body['data'] ?? $body;
                $version = $payload['version'] ?? $body['version'] ?? null;
                $data = $payload['data'] ?? $body['data'] ?? [];

                return [
                    'status' => SyncStatus::UPDATED,
                    'version' => $version !== null ? (string) $version : null,
                    'etag' => $response->header('ETag'),
                    'data' => is_array($data) ? $data : [],
                ];
            }

            return [
                'status' => SyncStatus::FAILED,
                'version' => null,
                'etag' => null,
                'data' => null,
            ];
        } catch (\Throwable $e) {
            Log::warning("Sync offline for {$resource->value}: {$e->getMessage()}");

            return [
                'status' => SyncStatus::OFFLINE,
                'version' => $syncVersion?->version,
                'etag' => $currentEtag,
                'data' => null,
            ];
        }
    }

    public function markSynced(SyncResource $resource, string $version, ?string $etag): void
    {
        SyncVersion::updateOrCreate(
            ['resource' => $resource->value],
            [
                'version' => $version,
                'etag' => $etag,
                'last_synced_at' => now(),
                'next_sync_after' => now()->addMinutes($resource->syncIntervalMinutes()),
            ],
        );
    }
}
