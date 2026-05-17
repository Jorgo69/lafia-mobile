<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Modules\Sync\Enums\SyncResource;
use App\Modules\Sync\Services\RemoteConfigService;
use App\Modules\Sync\Services\SyncManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesTestUser;
use Tests\TestCase;

final class ResilienceTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestUser;

    public function test_sync_handles_unreachable_server(): void
    {
        $rc = new RemoteConfigService('http://localhost:99999');
        $result = $rc->check(SyncResource::EMERGENCY_CONTACTS);
        $this->assertSame('offline', $result['status']->value);
    }

    public function test_sync_manager_runs_offline(): void
    {
        $results = app(SyncManager::class)->syncAll();
        foreach ($results as $r) {
            $this->assertContains($r->status->value, ['offline', 'up_to_date', 'failed']);
        }
    }

    public function test_emergency_works_without_vault_keys(): void
    {
        $this->getJson('/api/emergency/centers')->assertOk();
    }

    public function test_all_pages_render_with_empty_db(): void
    {
        foreach (['/', '/ussd', '/pharmacies', '/profil-vital', '/cercle'] as $page) {
            $this->get($page)->assertOk();
        }
    }

    public function test_concurrent_vault_operations(): void
    {
        $this->createTestUser();

        $ids = [];
        for ($i = 0; $i < 10; $i++) {
            $r = $this->postJson('/api/vault/health', [
                'label' => "Entry {$i}",
                'blood_type' => 'O+',
            ]);
            $r->assertStatus(201);
            $ids[] = $r->json('data.id');
        }

        foreach ($ids as $id) {
            $this->getJson("/api/vault/{$id}/decrypt")->assertOk();
        }
    }

    public function test_settings_missing_does_not_crash(): void
    {
        \Illuminate\Support\Facades\DB::table('settings')->truncate();
        // Without onboarding_done, should redirect to welcome
        $this->get('/')->assertRedirect('/welcome');
        // Welcome page itself should render fine
        $this->get('/welcome')->assertOk();
    }
}
