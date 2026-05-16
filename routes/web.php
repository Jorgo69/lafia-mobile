<?php

use App\Livewire\CercleConfiance;
use App\Livewire\Dashboard;
use App\Livewire\PharmacieGarde;
use App\Livewire\ProfilVital;
use App\Livewire\Settings;
use App\Livewire\UssdGuide;
use Illuminate\Support\Facades\Route;

Route::get('/', Dashboard::class)->name('dashboard');
Route::get('/ussd', UssdGuide::class)->name('ussd');
Route::get('/pharmacies', PharmacieGarde::class)->name('pharmacies');
Route::get('/profil-vital', ProfilVital::class)->name('profil-vital');
Route::get('/cercle', CercleConfiance::class)->name('cercle');
Route::get('/settings', Settings::class)->name('settings');
