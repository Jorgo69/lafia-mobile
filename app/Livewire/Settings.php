<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Modules\Sync\Enums\SyncResource;
use App\Modules\Sync\Models\SyncSnapshot;
use App\Modules\Sync\Models\SyncVersion;
use App\Modules\Sync\Services\SyncManager;
use App\Services\Settings\SettingsService;
use Illuminate\Support\Facades\App;
use Livewire\Component;

final class Settings extends Component
{
    public bool $isDark;
    public string $locale;
    public bool $lockEnabled;
    public string $lockInterval;
    public bool $showRollbackSuccess = false;

    /** @var array<string, string> */
    private const LOCALE_LABELS = [
        'fr' => 'Francais',
        'en' => 'English',
        'yo' => 'Yoruba',
        'fon' => 'Fɔngbè',
        'ddn' => 'Dendi',
        'bba' => 'Bariba',
    ];

    /** @return array<string, string> */
    private static function resourceLabels(): array
    {
        return [
            'emergency_contacts' => __('common.emergencies'),
            'short_codes' => __('ussd.title'),
            'pharmacies' => __('pharma.title'),
            'practical_tips' => __('common.tips'),
            'health_schema' => __('common.health_schema'),
        ];
    }

    public function mount(): void
    {
        $settings = app(SettingsService::class);
        $this->isDark = $settings->isDarkMode();
        $this->locale = App::getLocale();
        $this->lockEnabled = $settings->isLockEnabled();
        $this->lockInterval = $settings->get('lock_interval', '1') ?? '1';
    }

    public function toggleDarkMode(): void
    {
        $this->isDark = !$this->isDark;
        app(SettingsService::class)->set('dark_mode', $this->isDark ? '1' : '0');
        $this->js('window.dispatchEvent(new Event("dark-mode-changed"))');
    }

    public function setLocale(string $locale): void
    {
        $available = config('app.available_locales', ['fr']);
        if (!in_array($locale, $available, true)) {
            return;
        }

        $this->locale = $locale;
        App::setLocale($locale);
        app(SettingsService::class)->set('locale', $locale);

        $this->js('window.location.replace(window.location.href)');
    }

    public function toggleLock(): void
    {
        $this->lockEnabled = !$this->lockEnabled;
        app(SettingsService::class)->set('lock_enabled', $this->lockEnabled ? '1' : '0');
    }

    public function setLockInterval(string $interval): void
    {
        if (!in_array($interval, ['0', '1', '5', '15'], true)) {
            return;
        }

        $this->lockInterval = $interval;
        app(SettingsService::class)->set('lock_interval', $interval);
    }

    public function rollbackResource(string $resourceValue): void
    {
        $resource = SyncResource::tryFrom($resourceValue);

        if ($resource === null) {
            return;
        }

        $syncManager = app(SyncManager::class);
        $syncManager->rollback($resource);
        $this->showRollbackSuccess = true;
    }

    /**
     * @return array<int, array{key: string, label: string, version: string, has_snapshot: bool, snapshot_date: ?string}>
     */
    private function getDatasets(): array
    {
        $datasets = [];

        foreach (self::resourceLabels() as $key => $label) {
            $syncVersion = SyncVersion::find($key);
            $snapshot = SyncSnapshot::find($key);

            $datasets[] = [
                'key' => $key,
                'label' => $label,
                'version' => $syncVersion?->version ?? '-',
                'has_snapshot' => $snapshot !== null,
                'snapshot_date' => $snapshot?->created_at?->diffForHumans(),
            ];
        }

        return $datasets;
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.settings', [
            'locales' => self::LOCALE_LABELS,
            'appVersion' => '1.0.0',
            'datasets' => $this->getDatasets(),
        ])->layout('components.layouts.app', ['title' => __('common.settings')]);
    }
}
