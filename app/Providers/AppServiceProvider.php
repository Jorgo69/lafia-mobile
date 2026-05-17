<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        if (env('NATIVEPHP_PLATFORM') === 'android') {
            // The desktop NativeServiceProvider (nativephp/laravel) overrides
            // database.default to 'nativephp' pointing to the ephemeral bundle dir.
            // On mobile, DB_DATABASE is set by LaravelEnvironment.kt to persisted storage.
            // We restore the correct connection after all providers have booted.
            $this->app->booted(function () {
                config(['database.default' => 'sqlite']);
                $this->runMigrationsIfNeeded();
            });
        }
    }

    private function runMigrationsIfNeeded(): void
    {
        try {
            \DB::table('settings')->count();
        } catch (\Throwable) {
            Artisan::call('migrate', ['--force' => true]);
        }

        $this->fixDataIntegrity();

        if (\DB::table('emergency_centers')->count() === 0) {
            $seeders = [
                \App\Modules\Emergency\Database\Seeders\EmergencySeeder::class,
                \App\Modules\Emergency\Database\Seeders\GpsCoordinatesSeeder::class,
                \App\Modules\Ussd\Database\Seeders\UssdSeeder::class,
                \App\Modules\Pharmacy\Database\Seeders\PharmacySeeder::class,
                \App\Modules\Tips\Database\Seeders\PracticalTipSeeder::class,
            ];
            foreach ($seeders as $seeder) {
                Artisan::call('db:seed', ['--class' => $seeder, '--force' => true]);
            }
        }
    }

    private function fixDataIntegrity(): void
    {
        // Fix Police Republicaine phone: was 160 or 117, should be 116
        $policeCenter = \DB::table('emergency_centers')
            ->where('slug', 'police-republicaine-national')
            ->first();

        if ($policeCenter !== null) {
            $wrong = \DB::table('emergency_contacts')
                ->where('emergency_center_id', $policeCenter->id)
                ->whereIn('phone_number', ['160', '117'])
                ->exists();

            if ($wrong) {
                \DB::table('emergency_contacts')
                    ->where('emergency_center_id', $policeCenter->id)
                    ->update(['phone_number' => '116']);
            }
        }
    }
}
