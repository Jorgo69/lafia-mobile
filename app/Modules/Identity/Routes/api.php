<?php

declare(strict_types=1);

use App\Modules\Identity\Controllers\IdentityController;
use Illuminate\Support\Facades\Route;

Route::prefix('identity')->name('identity.')->group(function () {
    Route::get('/', [IdentityController::class, 'show'])->name('show');
    Route::post('/register', [IdentityController::class, 'register'])->name('register');
    Route::post('/{identityId}/guardians', [IdentityController::class, 'addGuardian'])->name('guardians.add');

    Route::prefix('recovery')->name('recovery.')->group(function () {
        Route::post('/request', [IdentityController::class, 'requestRecovery'])->name('request');
        Route::post('/{recoveryRequestId}/approve', [IdentityController::class, 'approveRecovery'])->name('approve');
        Route::get('/{recoveryRequestId}/status', [IdentityController::class, 'recoveryStatus'])->name('status');
    });
});
