<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Modules\Emergency\Models\Department;
use App\Modules\Emergency\Models\EmergencyCenter;
use App\Modules\Emergency\Models\EmergencyContact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesTestUser;
use Tests\TestCase;

final class BruteForceTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestUser;

    public function test_rapid_vault_access_does_not_crash(): void
    {
        $this->createTestUser();

        $store = $this->postJson('/api/vault/health', [
            'label' => 'Stress test',
            'blood_type' => 'O+',
        ]);
        $store->assertStatus(201);
        $id = $store->json('data.id');

        for ($i = 0; $i < 20; $i++) {
            $r = $this->getJson("/api/vault/{$id}/decrypt");
            $this->assertContains($r->status(), [200, 429]);
        }
    }

    public function test_invalid_vault_ids_return_error(): void
    {
        foreach ([0, 99999, PHP_INT_MAX] as $id) {
            $r = $this->getJson("/api/vault/{$id}/decrypt");
            $this->assertContains($r->status(), [404, 422, 500]);
        }
    }

    public function test_no_stack_trace_in_production(): void
    {
        config(['app.debug' => false]);
        $r = $this->getJson('/api/vault/99999/decrypt');
        $this->assertStringNotContainsString('vendor/', $r->getContent());
    }

    public function test_emergency_report_spam_does_not_crash(): void
    {
        $dept = Department::create(['name' => 'Test', 'code' => 'test']);
        $center = EmergencyCenter::create([
            'department_id' => $dept->id, 'name' => 'Spam', 'slug' => 'spam',
            'type' => 'ccpc', 'category' => 'civil_protection', 'is_active' => true,
        ]);
        $contact = EmergencyContact::create([
            'emergency_center_id' => $center->id, 'operator' => 'mtn',
            'phone_number' => '+22900000000', 'priority_score' => 50,
            'provider_routing' => 'local_ccpc', 'is_active' => true,
        ]);

        for ($i = 0; $i < 30; $i++) {
            $this->postJson('/api/emergency/report', [
                'emergency_contact_id' => $contact->id,
                'reported_issue' => "Spam #{$i}",
            ]);
        }

        $contact->refresh();
        $this->assertTrue($contact->is_active);
    }
}
