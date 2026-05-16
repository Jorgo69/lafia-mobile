<?php

use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    App\Shared\Bus\BusServiceProvider::class,
    App\Services\Crypto\CryptoServiceProvider::class,
    App\Modules\Emergency\Providers\EmergencyServiceProvider::class,
    App\Modules\Vault\Providers\VaultServiceProvider::class,
    App\Modules\Identity\Providers\IdentityServiceProvider::class,
    App\Modules\Sync\Providers\SyncServiceProvider::class,
    App\Modules\Ussd\Providers\UssdServiceProvider::class,
    App\Modules\Pharmacy\Providers\PharmacyServiceProvider::class,
    App\Modules\Tips\Providers\TipsServiceProvider::class,
];
