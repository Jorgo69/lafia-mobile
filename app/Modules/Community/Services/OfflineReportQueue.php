<?php

declare(strict_types=1);

namespace App\Modules\Community\Services;

use App\Modules\Community\Enums\ReportStatus;
use App\Modules\Community\Models\PendingReport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class OfflineReportQueue
{
    private string $apiUrl;
    private string $deviceId;

    public function __construct()
    {
        $this->apiUrl = config('services.lafia_sync.url', 'https://api.lafia.bj');
        $this->deviceId = $this->getDeviceId();
    }

    /**
     * Queue a new report locally. Works offline.
     */
    public function queue(
        string $targetType,
        string $targetId,
        string $targetLabel,
        string $reportType,
        ?string $details = null,
        ?float $latitude = null,
        ?float $longitude = null,
    ): PendingReport {
        return PendingReport::create([
            'target_type' => $targetType,
            'target_id' => $targetId,
            'target_label' => $targetLabel,
            'report_type' => $reportType,
            'details' => $details,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'observed_at' => now(),
            'status' => ReportStatus::QUEUED,
        ]);
    }

    /**
     * Try to send all queued reports. Returns count of successfully sent.
     */
    public function flush(): int
    {
        $pending = PendingReport::queued()->orderBy('observed_at')->get();

        if ($pending->isEmpty()) {
            return 0;
        }

        $sent = 0;

        foreach ($pending as $report) {
            if ($this->send($report)) {
                $sent++;
            }
        }

        if ($sent > 0) {
            Log::info("OfflineReportQueue: {$sent}/{$pending->count()} reports sent.");
        }

        return $sent;
    }

    /**
     * Send a single report to the API.
     */
    private function send(PendingReport $report): bool
    {
        $report->markSending();

        try {
            $response = Http::timeout(10)
                ->post("{$this->apiUrl}/api/reports", [
                    'device_id' => $this->deviceId,
                    'target_type' => $report->target_type,
                    'target_id' => $report->target_id,
                    'report_type' => $report->report_type,
                    'details' => $report->details,
                    'latitude' => $report->latitude,
                    'longitude' => $report->longitude,
                    'observed_at' => $report->observed_at->toIso8601String(),
                ]);

            if ($response->successful()) {
                $report->markSent();
                return true;
            }

            $report->markFailed("HTTP {$response->status()}: " . ($response->json('message') ?? 'Unknown error'));
            return false;
        } catch (\Throwable $e) {
            $report->markFailed($e->getMessage());
            return false;
        }
    }

    /**
     * @return Collection<int, PendingReport>
     */
    public function getPending(): Collection
    {
        return PendingReport::unsent()->orderByDesc('observed_at')->get();
    }

    /**
     * @return Collection<int, PendingReport>
     */
    public function getRecentlySent(int $limit = 5): Collection
    {
        return PendingReport::where('status', ReportStatus::SENT)
            ->orderByDesc('sent_at')
            ->limit($limit)
            ->get();
    }

    public function pendingCount(): int
    {
        return PendingReport::queued()->count();
    }

    private function getDeviceId(): string
    {
        $settings = app(\App\Services\Settings\SettingsService::class);
        $deviceId = $settings->get('device_id');

        if ($deviceId === null) {
            $deviceId = (string) \Illuminate\Support\Str::uuid();
            $settings->set('device_id', $deviceId);
        }

        return $deviceId;
    }
}
