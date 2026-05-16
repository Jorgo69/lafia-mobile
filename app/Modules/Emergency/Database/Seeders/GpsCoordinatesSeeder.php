<?php

declare(strict_types=1);

namespace App\Modules\Emergency\Database\Seeders;

use App\Modules\Emergency\Models\EmergencyCenter;
use Illuminate\Database\Seeder;

final class GpsCoordinatesSeeder extends Seeder
{
    public function run(): void
    {
        $coordinates = $this->getCoordinates();

        foreach ($coordinates as $slug => $coords) {
            EmergencyCenter::where('slug', $slug)->update([
                'latitude' => $coords[0],
                'longitude' => $coords[1],
            ]);
        }

        $updated = EmergencyCenter::whereNotNull('latitude')->count();
        $this->command->info("GPS coordinates updated for {$updated} centers.");
    }

    /** @return array<string, array{0: float, 1: float}> */
    private function getCoordinates(): array
    {
        return [
            // Alibori
            'ccpc-banikoara' => [11.3000, 2.4333],
            'ccpc-malanville' => [11.8667, 3.3833],
            'ccpc-kandi' => [11.1314, 2.9383],

            // Atacora
            'ccpc-tanguieta' => [10.6167, 1.2667],
            'ccpc-natitingou' => [10.3042, 1.3792],

            // Atlantique
            'ccpc-allada' => [6.6667, 2.1500],
            'ccpc-ouidah' => [6.3667, 2.0833],
            'ccpc-abomey-calavi' => [6.4486, 2.3556],
            'ccpc-cococodji' => [6.3833, 2.3167],
            'ccpc-ouidah-gdiz' => [6.3500, 2.0500],

            // Borgou
            'ccpc-parakou' => [9.3370, 2.6286],

            // Collines
            'ccpc-dassa' => [7.7500, 2.1833],
            'ccpc-savalou' => [7.9283, 1.9756],

            // Donga
            'ccpc-djougou' => [9.7081, 1.6656],
            'ccpc-bassila' => [9.0167, 1.6667],

            // Littoral
            'ccpc-cotonou-st-jean' => [6.3654, 2.4183],
            'ccpc-cotonou-sodjatime' => [6.3500, 2.4000],
            'ccpc-cotonou-tokpa' => [6.3567, 2.4267],

            // Mono-Couffo
            'ccpc-lokossa' => [6.6333, 1.7167],
            'ccpc-dogbo' => [6.8000, 1.7833],
            'ccpc-come' => [6.4000, 1.8833],

            // Oueme-Plateau
            'ccpc-porto-novo' => [6.4969, 2.6289],
            'ccpc-seme-krake' => [6.3833, 2.6167],
            'ccpc-pobe' => [6.9833, 2.6667],

            // Zou
            'ccpc-bohicon' => [7.1783, 2.0667],
            'ccpc-cove' => [7.2200, 2.3333],
        ];
    }
}
