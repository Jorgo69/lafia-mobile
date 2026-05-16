<?php

declare(strict_types=1);

namespace App\Modules\Community\Jobs;

use App\Modules\Community\Services\OfflineReportQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

final class FlushPendingReportsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public int $tries = 1;
    public int $timeout = 30;

    public function handle(OfflineReportQueue $queue): void
    {
        $queue->flush();
    }
}
