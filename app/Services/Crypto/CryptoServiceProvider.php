<?php

declare(strict_types=1);

namespace App\Services\Crypto;

use Illuminate\Support\ServiceProvider;

final class CryptoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EncryptionService::class);

        $this->app->singleton(KeyPairManager::class, function () {
            return new KeyPairManager(
                storagePath: storage_path('app/vault/keys'),
            );
        });
    }
}
