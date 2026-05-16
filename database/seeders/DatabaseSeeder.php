<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Emergency\Database\Seeders\EmergencySeeder;
use App\Modules\Emergency\Database\Seeders\GpsCoordinatesSeeder;
use App\Modules\Ussd\Database\Seeders\UssdSeeder;
use App\Modules\Pharmacy\Database\Seeders\PharmacySeeder;
use App\Modules\Tips\Database\Seeders\PracticalTipSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call(EmergencySeeder::class);
        $this->call(GpsCoordinatesSeeder::class);
        $this->call(UssdSeeder::class);
        $this->call(PharmacySeeder::class);
        $this->call(PracticalTipSeeder::class);
    }
}
