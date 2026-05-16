<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Sync;

use App\Modules\Sync\Enums\SyncResource;
use App\Modules\Sync\Enums\SyncStatus;
use PHPUnit\Framework\TestCase;

final class SyncEnumsTest extends TestCase
{
    public function test_sync_resource_priorities(): void
    {
        $this->assertGreaterThan(
            SyncResource::GPS_COORDINATES->priority(),
            SyncResource::SHORT_CODES->priority(),
        );

        $this->assertGreaterThan(
            SyncResource::HEALTH_SCHEMA->priority(),
            SyncResource::EMERGENCY_CONTACTS->priority(),
        );
    }

    public function test_sync_resource_intervals(): void
    {
        // Short codes should sync more frequently than GPS
        $this->assertLessThan(
            SyncResource::GPS_COORDINATES->syncIntervalMinutes(),
            SyncResource::SHORT_CODES->syncIntervalMinutes(),
        );
    }

    public function test_sync_status_values(): void
    {
        $this->assertSame('up_to_date', SyncStatus::UP_TO_DATE->value);
        $this->assertSame('offline', SyncStatus::OFFLINE->value);
        $this->assertSame('failed', SyncStatus::FAILED->value);
        $this->assertSame('updated', SyncStatus::UPDATED->value);
    }
}
