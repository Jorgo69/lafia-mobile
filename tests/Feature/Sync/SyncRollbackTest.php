<?php

declare(strict_types=1);

namespace Tests\Feature\Sync;

use App\Modules\Sync\Enums\SyncResource;
use App\Modules\Sync\Handlers\PharmaciesSyncHandler;
use App\Modules\Sync\Models\SyncSnapshot;
use App\Modules\Sync\Models\SyncVersion;
use App\Modules\Sync\Services\SyncRollbackService;
use App\Modules\Pharmacy\Models\Pharmacy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class SyncRollbackTest extends TestCase
{
    use RefreshDatabase;

    private SyncRollbackService $service;
    private PharmaciesSyncHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SyncRollbackService();
        $this->handler = new PharmaciesSyncHandler();
        $this->service->registerHandler(SyncResource::PHARMACIES, $this->handler);
    }

    public function test_take_snapshot_saves_current_data(): void
    {
        // Seed some pharmacies
        $this->handler->apply([
            ['name' => 'Pharma Alpha', 'zone' => 'littoral', 'city' => 'Cotonou', 'phone' => '123'],
            ['name' => 'Pharma Beta', 'zone' => 'littoral', 'city' => 'Cotonou', 'phone' => '456'],
        ]);

        SyncVersion::create([
            'resource' => 'pharmacies',
            'version' => '1',
            'last_synced_at' => now(),
        ]);

        $this->service->takeSnapshot(SyncResource::PHARMACIES);

        $snapshot = SyncSnapshot::find('pharmacies');
        $this->assertNotNull($snapshot);
        $this->assertEquals('1', $snapshot->version);
        $this->assertEquals(2, $snapshot->item_count);
        $this->assertCount(2, $snapshot->data);
    }

    public function test_rollback_restores_previous_data(): void
    {
        // Initial data
        $this->handler->apply([
            ['name' => 'Pharma Alpha', 'zone' => 'littoral', 'city' => 'Cotonou', 'phone' => '123'],
            ['name' => 'Pharma Beta', 'zone' => 'littoral', 'city' => 'Cotonou', 'phone' => '456'],
        ]);

        SyncVersion::create([
            'resource' => 'pharmacies',
            'version' => '1',
            'last_synced_at' => now(),
        ]);

        // Snapshot before update
        $this->service->takeSnapshot(SyncResource::PHARMACIES);

        // Overwrite with new data
        Pharmacy::query()->delete();
        $this->handler->apply([
            ['name' => 'Pharma Gamma', 'zone' => 'atlantique', 'city' => 'Abomey', 'phone' => '789'],
        ]);

        SyncVersion::where('resource', 'pharmacies')->update(['version' => '2']);

        $this->assertEquals(1, Pharmacy::count());
        $this->assertEquals('Pharma Gamma', Pharmacy::first()->name);

        // Rollback
        $restored = $this->service->rollback(SyncResource::PHARMACIES);

        $this->assertEquals(2, $restored);
        $this->assertEquals(2, Pharmacy::count());
        $this->assertNotNull(Pharmacy::where('name', 'Pharma Alpha')->first());
        $this->assertNotNull(Pharmacy::where('name', 'Pharma Beta')->first());
        $this->assertNull(Pharmacy::where('name', 'Pharma Gamma')->first());

        // Version reverted
        $this->assertEquals('1', SyncVersion::find('pharmacies')->version);
    }

    public function test_validate_rejects_empty_dataset(): void
    {
        // Existing snapshot with 10 items
        SyncSnapshot::create([
            'resource' => 'pharmacies',
            'version' => '1',
            'data' => array_fill(0, 10, ['name' => 'test']),
            'item_count' => 10,
            'created_at' => now(),
        ]);

        $this->assertFalse($this->service->validateNewData(SyncResource::PHARMACIES, []));
    }

    public function test_validate_rejects_suspicious_drop(): void
    {
        SyncSnapshot::create([
            'resource' => 'pharmacies',
            'version' => '1',
            'data' => array_fill(0, 20, ['name' => 'test']),
            'item_count' => 20,
            'created_at' => now(),
        ]);

        // 4 items when 20 existed = 20% ratio, below 50% threshold
        $this->assertFalse($this->service->validateNewData(
            SyncResource::PHARMACIES,
            array_fill(0, 4, ['name' => 'test']),
        ));
    }

    public function test_validate_accepts_normal_update(): void
    {
        SyncSnapshot::create([
            'resource' => 'pharmacies',
            'version' => '1',
            'data' => array_fill(0, 20, ['name' => 'test']),
            'item_count' => 20,
            'created_at' => now(),
        ]);

        // 18 items when 20 existed = 90% ratio, above threshold
        $this->assertTrue($this->service->validateNewData(
            SyncResource::PHARMACIES,
            array_fill(0, 18, ['name' => 'test']),
        ));
    }

    public function test_validate_accepts_first_sync(): void
    {
        // No snapshot exists = first sync, always accept
        $this->assertTrue($this->service->validateNewData(
            SyncResource::PHARMACIES,
            [['name' => 'test']],
        ));
    }

    public function test_rollback_without_snapshot_returns_zero(): void
    {
        $this->assertEquals(0, $this->service->rollback(SyncResource::PHARMACIES));
    }

    public function test_has_snapshot(): void
    {
        $this->assertFalse($this->service->hasSnapshot(SyncResource::PHARMACIES));

        SyncSnapshot::create([
            'resource' => 'pharmacies',
            'version' => '1',
            'data' => [],
            'item_count' => 0,
            'created_at' => now(),
        ]);

        $this->assertTrue($this->service->hasSnapshot(SyncResource::PHARMACIES));
    }
}
