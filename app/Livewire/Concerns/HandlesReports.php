<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use App\Modules\Community\Jobs\FlushPendingReportsJob;
use App\Modules\Community\Services\OfflineReportQueue;
use Livewire\Attributes\On;

trait HandlesReports
{
    #[On('submit-report')]
    public function submitReport(string $targetType, string $targetId, string $targetLabel, string $reportType): void
    {
        $queue = app(OfflineReportQueue::class);

        $queue->queue(
            targetType: $targetType,
            targetId: $targetId,
            targetLabel: $targetLabel,
            reportType: $reportType,
        );

        FlushPendingReportsJob::dispatch();

        $this->dispatch('toast', message: __('common.report_sent'), variant: 'success');
    }
}
