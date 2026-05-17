<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Livewire\Concerns\RequiresAuth;
use App\Livewire\Enums\CoffreTab;
use App\Services\Settings\SettingsService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
final class Coffre extends Component
{
    use RequiresAuth;

    public CoffreTab $tab = CoffreTab::SANTE;

    public function mount(): void
    {
        $saved = app(SettingsService::class)->get('coffre_tab', CoffreTab::SANTE->value);
        $this->tab = CoffreTab::tryFrom($saved ?? '') ?? CoffreTab::SANTE;
    }

    public function setTab(string $value): void
    {
        $tab = CoffreTab::tryFrom($value);
        if ($tab === null) {
            return;
        }

        $this->tab = $tab;
        app(SettingsService::class)->set('coffre_tab', $tab->value);
    }

    public function render(): View
    {
        return view('livewire.coffre', [
            'tabs' => CoffreTab::cases(),
        ]);
    }
}
