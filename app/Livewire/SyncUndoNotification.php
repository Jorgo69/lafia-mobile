<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Modules\Sync\Enums\SyncResource;
use App\Modules\Sync\Services\SyncManager;
use App\Services\Settings\SettingsService;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
final class SyncUndoNotification extends Component
{
    public bool $visible = false;
    public string $message = '';

    /** @var array<int, array{resource: string, items: int}> */
    public array $pendingUpdates = [];

    public function mount(): void
    {
        $this->checkForUpdates();
    }

    public function checkForUpdates(): void
    {
        $settings = app(SettingsService::class);
        $raw = $settings->get('sync_undo_pending');

        if ($raw === null) {
            $this->visible = false;
            return;
        }

        $data = json_decode($raw, true);

        if (!is_array($data) || empty($data['updates'])) {
            $this->visible = false;
            return;
        }

        // Expire after 30 seconds
        $at = $data['at'] ?? null;
        if ($at !== null && now()->diffInSeconds($at) > 30) {
            $settings->set('sync_undo_pending', null);
            $this->visible = false;
            return;
        }

        $this->pendingUpdates = $data['updates'];
        $totalItems = array_sum(array_column($data['updates'], 'items'));
        $this->message = __('common.sync_updated', ['count' => $totalItems]);
        $this->visible = true;
    }

    public function undoAll(): void
    {
        $syncManager = app(SyncManager::class);

        foreach ($this->pendingUpdates as $update) {
            $resource = SyncResource::tryFrom($update['resource']);
            if ($resource !== null) {
                $syncManager->rollback($resource);
            }
        }

        app(SettingsService::class)->set('sync_undo_pending', null);
        $this->pendingUpdates = [];
        $this->visible = false;
        $this->message = '';
    }

    public function dismiss(): void
    {
        app(SettingsService::class)->set('sync_undo_pending', null);
        $this->visible = false;
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.sync-undo-notification');
    }
}
