<?php

declare(strict_types=1);

namespace App\Modules\Community\Services;

use App\Modules\Community\Enums\ReportStatus;
use App\Modules\Community\Models\PendingProposal;
use App\Services\Settings\SettingsService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

final class OfflineProposalQueue
{
    private string $apiUrl;
    private string $deviceId;

    public function __construct()
    {
        $this->apiUrl = config('services.lafia_sync.url', 'https://api.lafia.bj');
        $this->deviceId = $this->getDeviceId();
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function queue(
        string $proposalType,
        array $payload,
        ?string $reason = null,
        ?float $latitude = null,
        ?float $longitude = null,
    ): PendingProposal {
        return PendingProposal::create([
            'proposal_type' => $proposalType,
            'payload' => $payload,
            'reason' => $reason,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'status' => ReportStatus::QUEUED,
        ]);
    }

    public function flush(): int
    {
        $pending = PendingProposal::queued()->orderBy('created_at')->get();

        if ($pending->isEmpty()) {
            return 0;
        }

        $sent = 0;

        foreach ($pending as $proposal) {
            if ($this->send($proposal)) {
                $sent++;
            }
        }

        if ($sent > 0) {
            Log::info("OfflineProposalQueue: {$sent}/{$pending->count()} proposals sent.");
        }

        return $sent;
    }

    private function send(PendingProposal $proposal): bool
    {
        $proposal->markSending();

        try {
            $response = Http::timeout(10)
                ->post("{$this->apiUrl}/api/proposals", [
                    'device_id' => $this->deviceId,
                    'dataset' => $proposal->proposal_type->datasetSlug(),
                    'proposal_type' => $proposal->proposal_type->value,
                    'diff' => $proposal->payload,
                    'reason' => $proposal->reason,
                    'latitude' => $proposal->latitude,
                    'longitude' => $proposal->longitude,
                ]);

            if ($response->successful()) {
                $proposal->markSent($response->json('data.id', ''));
                return true;
            }

            $proposal->markFailed("HTTP {$response->status()}: " . ($response->json('message') ?? 'Unknown error'));
            return false;
        } catch (\Throwable $e) {
            $proposal->markFailed($e->getMessage());
            return false;
        }
    }

    /**
     * @return Collection<int, PendingProposal>
     */
    public function getPending(): Collection
    {
        return PendingProposal::unsent()->orderByDesc('created_at')->get();
    }

    public function pendingCount(): int
    {
        return PendingProposal::queued()->count();
    }

    private function getDeviceId(): string
    {
        $settings = app(SettingsService::class);
        $deviceId = $settings->get('device_id');

        if ($deviceId === null) {
            $deviceId = (string) Str::uuid();
            $settings->set('device_id', $deviceId);
        }

        return $deviceId;
    }
}
