<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Services\Settings\SettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PinProtectionTest extends TestCase
{
    use RefreshDatabase;

    private SettingsService $settings;

    protected function setUp(): void
    {
        parent::setUp();
        SettingsService::clearCache();
        $this->settings = app(SettingsService::class);
    }

    public function test_lock_disabled_by_default(): void
    {
        $this->assertFalse($this->settings->isLockEnabled());
    }

    public function test_enable_lock(): void
    {
        $this->settings->set('lock_enabled', '1');

        $this->assertTrue($this->settings->isLockEnabled());
    }

    public function test_session_not_active_by_default(): void
    {
        $this->assertFalse($this->settings->isPinSessionActive());
    }

    public function test_refresh_session_makes_it_active(): void
    {
        $this->settings->refreshPinSession();

        $this->assertTrue($this->settings->isPinSessionActive());
    }

    public function test_lock_interval_default(): void
    {
        $this->assertEquals(1, $this->settings->lockInterval());
    }

    public function test_lock_interval_custom(): void
    {
        $this->settings->set('lock_interval', '15');

        SettingsService::clearCache();

        $this->assertEquals(15, $this->settings->lockInterval());
    }

    public function test_session_respects_interval(): void
    {
        $this->settings->set('lock_interval', '0');
        SettingsService::clearCache();

        // Interval 0 = immediate, but refreshPinSession adds 0 minutes
        // which means session expires NOW, so it should not be active
        $this->settings->refreshPinSession();

        // With 0 interval, addMinutes(0) = now, which is not < now
        $this->assertFalse($this->settings->isPinSessionActive());
    }

    public function test_vault_page_accessible_when_lock_disabled(): void
    {
        $response = $this->get('/profil-vital');

        $response->assertStatus(200);
        $response->assertDontSee(__('vault.checking_auth'));
    }

    public function test_vault_page_shows_gate_when_lock_enabled(): void
    {
        $this->settings->set('lock_enabled', '1');

        $response = $this->get('/profil-vital');

        $response->assertStatus(200);
        $response->assertSee(__('vault.checking_auth'));
    }

    public function test_vault_page_skips_gate_when_session_active(): void
    {
        $this->settings->set('lock_enabled', '1');
        $this->settings->set('lock_interval', '5');
        SettingsService::clearCache();
        $this->settings->refreshPinSession();

        $response = $this->get('/profil-vital');

        $response->assertStatus(200);
        $response->assertDontSee(__('vault.checking_auth'));
    }
}
