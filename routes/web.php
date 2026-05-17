<?php

use App\Livewire\CercleConfiance;
use App\Livewire\Coffre;
use App\Livewire\Dashboard;
use App\Livewire\More;
use App\Livewire\PharmacieGarde;
use App\Livewire\ProfilVital;
use App\Livewire\Settings;
use App\Livewire\UssdGuide;
use App\Livewire\Welcome;
use Illuminate\Support\Facades\Route;

Route::get('/welcome', Welcome::class)->name('welcome');

Route::middleware(\App\Http\Middleware\EnsureOnboarded::class)->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/ussd', UssdGuide::class)->name('ussd');
    Route::get('/pharmacies', PharmacieGarde::class)->name('pharmacies');
    Route::get('/coffre', Coffre::class)->name('coffre');
    Route::get('/plus', More::class)->name('plus');
    Route::get('/settings', Settings::class)->name('settings');

    // Legacy direct access (still works)
    Route::get('/profil-vital', ProfilVital::class)->name('profil-vital');
    Route::get('/cercle', CercleConfiance::class)->name('cercle');
});
