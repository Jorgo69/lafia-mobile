<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\Settings\SettingsService;
use Illuminate\Support\Facades\App;
use Livewire\Component;

final class LanguageSwitcher extends Component
{
    public string $locale;

    /** @var array<string, string> */
    private const LOCALE_LABELS = [
        'fr' => 'FR',
        'en' => 'EN',
        'yo' => 'YO',
        'fon' => 'FƆ',
        'ddn' => 'DD',
        'bba' => 'BA',
    ];

    public function mount(): void
    {
        $this->locale = App::getLocale();
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

        $this->dispatch('locale-changed', locale: $locale);
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.language-switcher', [
            'locales' => self::LOCALE_LABELS,
        ]);
    }
}
