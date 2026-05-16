<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Modules\Vault\Models\Vault;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesTestUser;
use Tests\TestCase;

final class ZeroKnowledgeTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestUser();
    }

    public function test_vault_data_is_never_stored_in_plaintext(): void
    {
        $this->postJson('/api/vault/health', [
            'label' => 'Donnees sensibles',
            'blood_type' => 'AB+',
            'allergies' => ['aspirine', 'penicilline'],
            'emergency_notes' => 'VIH negatif, CNI 229123456789',
        ]);

        $vault = Vault::first();
        $raw = $vault->getRawOriginal('encrypted_payload');

        $this->assertStringNotContainsString('AB+', $raw);
        $this->assertStringNotContainsString('aspirine', $raw);
        $this->assertStringNotContainsString('penicilline', $raw);
        $this->assertStringNotContainsString('229123456789', $raw);
        $this->assertStringNotContainsString('blood_type', $raw);
    }

    public function test_encrypted_data_is_base64(): void
    {
        $this->postJson('/api/vault/health', [
            'label' => 'Test encoding',
            'blood_type' => 'O+',
        ]);

        $vault = Vault::first();
        $raw = $vault->getRawOriginal('encrypted_payload');
        $this->assertNotFalse(base64_decode($raw, true));
    }

    public function test_same_data_produces_different_ciphertexts(): void
    {
        $this->postJson('/api/vault/health', ['label' => 'E1', 'blood_type' => 'O+']);
        $this->postJson('/api/vault/health', ['label' => 'E2', 'blood_type' => 'O+']);

        $entries = Vault::all();
        $this->assertCount(2, $entries);
        $this->assertNotSame(
            $entries[0]->getRawOriginal('encrypted_payload'),
            $entries[1]->getRawOriginal('encrypted_payload'),
        );
    }
}
