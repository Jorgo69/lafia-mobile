<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesTestUser;
use Tests\TestCase;

final class VaultApiTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestUser();
    }

    public function test_store_health_data(): void
    {
        $this->postJson('/api/vault/health', [
            'label' => 'Groupe sanguin',
            'blood_type' => 'O+',
            'allergies' => ['penicilline'],
        ])->assertStatus(201);
    }

    public function test_store_then_decrypt(): void
    {
        $store = $this->postJson('/api/vault/health', [
            'label' => 'Test decrypt',
            'blood_type' => 'AB-',
            'emergency_notes' => 'Attention aspirine',
        ]);
        $store->assertStatus(201);
        $id = $store->json('data.id');

        $this->getJson("/api/vault/{$id}/decrypt")->assertOk();
    }

    public function test_list_vault_entries(): void
    {
        $this->postJson('/api/vault/health', ['label' => 'E1', 'blood_type' => 'A+']);
        $this->postJson('/api/vault/health', ['label' => 'E2', 'blood_type' => 'B-']);

        $this->getJson('/api/vault')->assertOk();
    }

    public function test_decrypt_nonexistent_entry(): void
    {
        $response = $this->getJson('/api/vault/99999/decrypt');
        $this->assertContains($response->status(), [404, 500]);
    }

    public function test_store_without_label_fails(): void
    {
        $this->postJson('/api/vault/health', [
            'blood_type' => 'O+',
        ])->assertStatus(422);
    }
}
