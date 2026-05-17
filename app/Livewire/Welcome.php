<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\Settings\SettingsService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.bare')]
final class Welcome extends Component
{
    public string $selectedLocale = 'fr';

    /** @var array<string, array{label: string, native: string}> */
    private const LOCALES = [
        'fr'  => ['label' => 'Français', 'native' => 'Bonjour'],
        'en'  => ['label' => 'English', 'native' => 'Hello'],
        'yo'  => ['label' => 'Yorùbá', 'native' => 'Ẹ kú'],
        'fon' => ['label' => 'Fongbé', 'native' => 'Afọ́n'],
        'ddn' => ['label' => 'Dendi', 'native' => 'Fofo'],
        'bba' => ['label' => 'Bariba', 'native' => 'Kúùrà'],
    ];

    public function selectLocale(string $locale): void
    {
        if (array_key_exists($locale, self::LOCALES)) {
            $this->selectedLocale = $locale;
        }
    }

    public function start(): void
    {
        $settings = app(SettingsService::class);
        $settings->set('locale', $this->selectedLocale);
        $settings->set('onboarding_done', '1');

        App::setLocale($this->selectedLocale);

        $this->redirect('/', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.welcome', [
            'locales' => self::LOCALES,
        ]);
    }
}
