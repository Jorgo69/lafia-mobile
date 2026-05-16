<?php

declare(strict_types=1);

namespace App\Modules\Pharmacy\Database\Seeders;

use App\Modules\Pharmacy\Models\Pharmacy;
use App\Modules\Pharmacy\Models\PharmacyGuard;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class PharmacySeeder extends Seeder
{
    public function run(): void
    {
        $pharmacies = [
            // === COTONOU (Littoral) ===
            ['name' => 'Pharmacie Camp Ghezo', 'zone' => 'littoral', 'city' => 'Cotonou', 'neighborhood' => 'Camp Ghezo', 'phone' => '+22921313535', 'latitude' => 6.3654, 'longitude' => 2.4183],
            ['name' => 'Pharmacie Adechina', 'zone' => 'littoral', 'city' => 'Cotonou', 'neighborhood' => 'Ganhi', 'phone' => '+22921321565', 'latitude' => 6.3617, 'longitude' => 2.4253],
            ['name' => 'Pharmacie des 4 Therapies', 'zone' => 'littoral', 'city' => 'Cotonou', 'neighborhood' => 'Tokpa Hoho', 'phone' => '+22921323330', 'latitude' => 6.3590, 'longitude' => 2.4290],
            ['name' => 'Pharmacie Haie Vive', 'zone' => 'littoral', 'city' => 'Cotonou', 'neighborhood' => 'Haie Vive', 'phone' => '+22921301025', 'latitude' => 6.3580, 'longitude' => 2.3985],
            ['name' => 'Pharmacie La Beninoise', 'zone' => 'littoral', 'city' => 'Cotonou', 'neighborhood' => 'Akpakpa Sodjatinme', 'phone' => '+22960502956', 'latitude' => 6.3632, 'longitude' => 2.4478],
            ['name' => 'Pharmacie Agbodedosedekon', 'zone' => 'littoral', 'city' => 'Cotonou', 'neighborhood' => 'Agbodedosedekon', 'phone' => '+22921335445', 'latitude' => 6.3700, 'longitude' => 2.4050],
            ['name' => 'Pharmacie Adetona-Lom\'Nava', 'zone' => 'littoral', 'city' => 'Cotonou', 'neighborhood' => 'Lom Nava', 'phone' => '+22994012397', 'latitude' => 6.3643, 'longitude' => 2.4158],
            ['name' => 'Pharmacie Forum Sante', 'zone' => 'littoral', 'city' => 'Cotonou', 'neighborhood' => 'Fidjrosse', 'phone' => '+22922050546', 'latitude' => 6.3450, 'longitude' => 2.3720],
            ['name' => 'Pharmacie Jonquet', 'zone' => 'littoral', 'city' => 'Cotonou', 'neighborhood' => 'Jonquet', 'phone' => '+22921312626', 'latitude' => 6.3625, 'longitude' => 2.4315],
            ['name' => 'Pharmacie Etoile', 'zone' => 'littoral', 'city' => 'Cotonou', 'neighborhood' => 'Etoile Rouge', 'phone' => '+22921310800', 'latitude' => 6.3670, 'longitude' => 2.4020],
            ['name' => 'Pharmacie du Lac', 'zone' => 'littoral', 'city' => 'Cotonou', 'neighborhood' => 'Dantokpa', 'phone' => '+22921322085', 'latitude' => 6.3685, 'longitude' => 2.4275],
            ['name' => 'Pharmacie Sainte Cecile', 'zone' => 'littoral', 'city' => 'Cotonou', 'neighborhood' => 'Cadjehoun', 'phone' => '+22921304545', 'latitude' => 6.3560, 'longitude' => 2.3900],
            ['name' => 'Pharmacie de Akpakpa', 'zone' => 'littoral', 'city' => 'Cotonou', 'neighborhood' => 'Akpakpa', 'phone' => '+22921330102', 'latitude' => 6.3620, 'longitude' => 2.4500],
            ['name' => 'Pharmacie Benin Pharma', 'zone' => 'littoral', 'city' => 'Cotonou', 'neighborhood' => 'Zongo', 'phone' => '+22921320800', 'latitude' => 6.3710, 'longitude' => 2.4150],
            ['name' => 'Pharmacie des Cocotiers', 'zone' => 'littoral', 'city' => 'Cotonou', 'neighborhood' => 'Cocotiers', 'phone' => '+22921313030', 'latitude' => 6.3570, 'longitude' => 2.4050],

            // === ABOMEY-CALAVI (Atlantique) ===
            ['name' => 'Pharmacie Ahouandogbo', 'zone' => 'atlantique', 'city' => 'Abomey-Calavi', 'neighborhood' => 'Calavi Centre', 'phone' => '+22921360202', 'latitude' => 6.4480, 'longitude' => 2.3550],
            ['name' => 'Pharmacie de Godomey', 'zone' => 'atlantique', 'city' => 'Abomey-Calavi', 'neighborhood' => 'Godomey', 'phone' => '+22921360505', 'latitude' => 6.4100, 'longitude' => 2.3500],
            ['name' => 'Pharmacie Tokpa Hoho', 'zone' => 'atlantique', 'city' => 'Abomey-Calavi', 'neighborhood' => 'Togba', 'phone' => '+22997150000', 'latitude' => 6.4350, 'longitude' => 2.3480],
            ['name' => 'Pharmacie les Graces', 'zone' => 'atlantique', 'city' => 'Abomey-Calavi', 'neighborhood' => 'Zogbadje', 'phone' => '+22996800000', 'latitude' => 6.4520, 'longitude' => 2.3420],

            // === PORTO-NOVO (Oueme) ===
            ['name' => 'Pharmacie Centrale Porto-Novo', 'zone' => 'oueme', 'city' => 'Porto-Novo', 'neighborhood' => 'Centre Ville', 'phone' => '+22920213030', 'latitude' => 6.4969, 'longitude' => 2.6289],
            ['name' => 'Pharmacie Ouando', 'zone' => 'oueme', 'city' => 'Porto-Novo', 'neighborhood' => 'Ouando', 'phone' => '+22920215050', 'latitude' => 6.4850, 'longitude' => 2.6200],

            // === PARAKOU (Borgou) ===
            ['name' => 'Pharmacie Arzeke', 'zone' => 'borgou', 'city' => 'Parakou', 'neighborhood' => 'Arzeke', 'phone' => '+22923610202', 'latitude' => 9.3370, 'longitude' => 2.6300],
            ['name' => 'Pharmacie du Borgou', 'zone' => 'borgou', 'city' => 'Parakou', 'neighborhood' => 'Centre', 'phone' => '+22923610505', 'latitude' => 9.3400, 'longitude' => 2.6250],

            // === BOHICON (Zou) ===
            ['name' => 'Pharmacie de Bohicon', 'zone' => 'zou', 'city' => 'Bohicon', 'neighborhood' => 'Centre', 'phone' => '+22922510101', 'latitude' => 7.1780, 'longitude' => 2.0660],

            // === NATITINGOU (Atacora) ===
            ['name' => 'Pharmacie de Natitingou', 'zone' => 'atacora', 'city' => 'Natitingou', 'neighborhood' => 'Centre', 'phone' => '+22923821010', 'latitude' => 10.3040, 'longitude' => 1.3790],

            // === DJOUGOU (Donga) ===
            ['name' => 'Pharmacie de Djougou', 'zone' => 'donga', 'city' => 'Djougou', 'neighborhood' => 'Centre', 'phone' => '+22923801010', 'latitude' => 9.7080, 'longitude' => 1.6660],

            // === LOKOSSA (Mono) ===
            ['name' => 'Pharmacie de Lokossa', 'zone' => 'mono', 'city' => 'Lokossa', 'neighborhood' => 'Centre', 'phone' => '+22922411010', 'latitude' => 6.6450, 'longitude' => 1.7170],

            // === COME (Mono) ===
            ['name' => 'Pharmacie Divine Misericorde', 'zone' => 'mono', 'city' => 'Come', 'neighborhood' => 'Akodeha', 'phone' => '+22952475858', 'latitude' => 6.4070, 'longitude' => 1.8800],
        ];

        foreach ($pharmacies as $data) {
            Pharmacy::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                $data,
            );
        }

        // Seed exemple de tours de garde (semaine courante + prochaine)
        $pharmacyIds = Pharmacy::where('zone', 'littoral')->pluck('id')->all();

        if (count($pharmacyIds) >= 5) {
            $currentWeekStart = now()->startOfWeek();
            $currentWeekEnd = now()->endOfWeek();
            $nextWeekStart = now()->addWeek()->startOfWeek();
            $nextWeekEnd = now()->addWeek()->endOfWeek();

            // Semaine courante: 5 pharmacies de garde
            $guardsThisWeek = array_slice($pharmacyIds, 0, 5);
            foreach ($guardsThisWeek as $pid) {
                PharmacyGuard::updateOrCreate(
                    ['pharmacy_id' => $pid, 'start_date' => $currentWeekStart->toDateString()],
                    [
                        'end_date' => $currentWeekEnd->toDateString(),
                        'zone' => 'littoral',
                    ],
                );
            }

            // Semaine prochaine: 5 pharmacies differentes
            $guardsNextWeek = array_slice($pharmacyIds, 5, 5);
            foreach ($guardsNextWeek as $pid) {
                PharmacyGuard::updateOrCreate(
                    ['pharmacy_id' => $pid, 'start_date' => $nextWeekStart->toDateString()],
                    [
                        'end_date' => $nextWeekEnd->toDateString(),
                        'zone' => 'littoral',
                    ],
                );
            }
        }
    }
}
