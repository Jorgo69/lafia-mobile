<?php

namespace App\Providers;

use App\Modules\Community\Jobs\FlushPendingReportsJob;
use App\Modules\Sync\Jobs\RunSyncJob;
use Native\Laravel\Facades\MenuBar;
use Native\Laravel\Facades\Window;
use Native\Laravel\Contracts\ProvidesPhpIni;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    public function boot(): void
    {
        Window::open('main')
            ->title('Lafia - Secours & Protection Civile')
            ->width(420)
            ->height(780)
            ->minWidth(360)
            ->minHeight(600);

        MenuBar::create()
            ->label('SOS')
            ->route('dashboard')
            ->width(360)
            ->height(500);

        // Sync silencieuse au demarrage — ne bloque pas l'UI
        RunSyncJob::dispatch();

        // Envoyer les signalements en attente
        FlushPendingReportsJob::dispatch();
    }

    public function phpIni(): array
    {
        return [
            'memory_limit' => '256M',
        ];
    }
}
