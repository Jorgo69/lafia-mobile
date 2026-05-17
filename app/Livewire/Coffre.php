<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Livewire\Concerns\RequiresAuth;
use App\Services\Settings\SettingsService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
final class Coffre extends Component
{
    use RequiresAuth;

    public string $tab = 'sante';

    public function mount(): void
    {
        $this->tab = app(SettingsService::class)->get('coffre_tab', 'sante') ?? 'sante';
    }

    public function setTab(string $tab): void
    {
        if (!in_array($tab, ['sante', 'gardiens'], true)) {
            return;
        }

        $this->tab = $tab;
        app(SettingsService::class)->set('coffre_tab', $tab);
    }

    public function render(): View
    {
        return view('livewire.coffre');
    }
}
