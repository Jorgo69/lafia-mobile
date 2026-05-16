<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesTestUser;
use Tests\TestCase;

final class IdentityApiTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestUser();
    }

    public function test_register_device(): void
    {
        $this->postJson('/api/identity/register', [
            'device_uuid' => 'test-device-' . uniqid(),
            'device_name' => 'Samsung A54',
            'device_platform' => 'android',
        ])->assertStatus(201);
    }

    public function test_register_without_device_uuid_fails(): void
    {
        $this->postJson('/api/identity/register', [
            'device_name' => 'Test',
        ])->assertStatus(422);
    }

    public function test_show_identity(): void
    {
        $this->postJson('/api/identity/register', [
            'device_uuid' => 'show-test-' . uniqid(),
        ]);

        $this->getJson('/api/identity')->assertOk();
    }
}
