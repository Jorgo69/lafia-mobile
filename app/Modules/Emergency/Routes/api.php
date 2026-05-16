<?php

declare(strict_types=1);

use App\Modules\Emergency\Controllers\EmergencyController;
use Illuminate\Support\Facades\Route;

Route::prefix('emergency')->name('emergency.')->group(function () {
    Route::get('/centers', [EmergencyController::class, 'index'])->name('centers.index');
    Route::get('/centers/nearest', [EmergencyController::class, 'nearest'])->name('centers.nearest');
    Route::get('/centers/operator/{operator}', [EmergencyController::class, 'byOperator'])->name('centers.by-operator');
    Route::post('/report', [EmergencyController::class, 'reportUpdate'])->name('report');
});
