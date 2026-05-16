<?php

declare(strict_types=1);

use App\Modules\Vault\Controllers\VaultController;
use Illuminate\Support\Facades\Route;

Route::prefix('vault')->name('vault.')->group(function () {
    Route::get('/', [VaultController::class, 'index'])->name('index');
    Route::post('/health', [VaultController::class, 'storeHealth'])->name('health.store');
    Route::get('/{vaultId}/decrypt', [VaultController::class, 'showDecrypted'])->name('decrypt');
});
