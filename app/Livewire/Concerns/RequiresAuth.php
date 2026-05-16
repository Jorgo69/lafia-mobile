<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use App\Services\Settings\SettingsService;

/**
 * Protects sensitive Livewire components with device authentication.
 *
 * Uses the phone's native lock (Face ID, fingerprint, PIN, pattern, password)
 * via NativePHP System::promptTouchID(). No custom PIN — the OS handles it.
 *
 * Behavior (like WhatsApp):
 * - User enables lock in Settings
 * - On Vault/Circle access: system auth prompt
 * - Session lasts for the configured interval (1/5/15 min)
 * - If lock disabled or NativePHP not available: no gate
 */
trait RequiresAuth
{
    public bool $authUnlocked = false;
    public bool $authRequired = false;

    public function mountRequiresAuth(): void
    {
        $settings = app(SettingsService::class);

        // Lock not enabled? Pass through.
        if ($settings->get('lock_enabled') !== '1') {
            $this->authUnlocked = true;
            return;
        }

        // Session still active? Pass through.
        if ($settings->isPinSessionActive()) {
            $this->authUnlocked = true;
            $settings->refreshPinSession();
            return;
        }

        // Need auth — JS will trigger the system prompt
        $this->authRequired = true;
        $this->authUnlocked = false;
    }

    /**
     * Called from Alpine when system auth succeeds.
     */
    public function authSuccess(): void
    {
        $this->authUnlocked = true;
        $this->authRequired = false;
        app(SettingsService::class)->refreshPinSession();
    }

    /**
     * Called from Alpine when system auth is not available (web browser, old device).
     * We let the user through — the lock is best-effort, not a hard block.
     */
    public function authUnavailable(): void
    {
        $this->authUnlocked = true;
        $this->authRequired = false;
    }
}
