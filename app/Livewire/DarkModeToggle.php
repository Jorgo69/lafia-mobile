<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\Settings\SettingsService;
use Livewire\Component;

final class DarkModeToggle extends Component
{
    public bool $isDark = false;

    public function mount(): void
    {
        $this->isDark = app(SettingsService::class)->isDarkMode();
    }

    public function toggle(): void
    {
        $this->isDark = app(SettingsService::class)->toggleDarkMode();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.dark-mode-toggle');
    }
}
