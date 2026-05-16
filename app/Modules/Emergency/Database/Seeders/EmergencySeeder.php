<?php

declare(strict_types=1);

namespace App\Modules\Emergency\Database\Seeders;

use App\Modules\Emergency\Enums\EmergencyCategory;
use App\Modules\Emergency\Enums\EmergencyCenterType;
use App\Shared\Enums\Operator;
use App\Modules\Emergency\Models\Department;
use App\Modules\Emergency\Models\EmergencyCenter;
use App\Modules\Emergency\Models\EmergencyContact;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class EmergencySeeder extends Seeder
{
    public function run(): void
    {
        $this->seedNationalShortCodes();
        $this->seedRegionalCenters();
    }

    private function seedNationalShortCodes(): void
    {
        $national = Department::create([
            'name' => 'National',
            'code' => 'national',
        ]);

        $shortCodes = [
            [
                'name' => 'Sapeurs-Pompiers',
                'slug' => 'sapeurs-pompiers-national',
                'category' => EmergencyCategory::FIRE,
                'type' => EmergencyCenterType::NATIONAL,
                'phone' => '118',
                'priority' => 100,
                'routing' => 'nearest_fire_station',
            ],
            [
                'name' => 'Police Secours',
                'slug' => 'police-secours-national',
                'category' => EmergencyCategory::POLICE,
                'type' => EmergencyCenterType::NATIONAL,
                'phone' => '117',
                'priority' => 95,
                'routing' => 'nearest_police_station',
            ],
            [
                'name' => 'SAMU - Urgences Medicales',
                'slug' => 'samu-national',
                'category' => EmergencyCategory::MEDICAL,
                'type' => EmergencyCenterType::NATIONAL,
                'phone' => '136',
                'priority' => 90,
                'routing' => 'nearest_hospital',
            ],
            [
                'name' => 'Police Republicaine',
                'slug' => 'police-republicaine-national',
                'category' => EmergencyCategory::POLICE,
                'type' => EmergencyCenterType::NATIONAL,
                'phone' => '160',
                'priority' => 85,
                'routing' => 'nearest_police_station',
            ],
            [
                'name' => 'Protection de l\'Enfant',
                'slug' => 'protection-enfant-national',
                'category' => EmergencyCategory::CHILD_PROTECTION,
                'type' => EmergencyCenterType::NATIONAL,
                'phone' => '111',
                'priority' => 80,
                'routing' => null,
            ],
        ];

        foreach ($shortCodes as $data) {
            $center = EmergencyCenter::create([
                'department_id' => $national->id,
                'name' => $data['name'],
                'slug' => $data['slug'],
                'type' => $data['type'],
                'category' => $data['category'],
            ]);

            EmergencyContact::create([
                'emergency_center_id' => $center->id,
                'operator' => Operator::MTN,
                'phone_number' => $data['phone'],
                'priority_score' => $data['priority'],
                'provider_routing' => $data['routing'],
            ]);

            EmergencyContact::create([
                'emergency_center_id' => $center->id,
                'operator' => Operator::MOOV,
                'phone_number' => $data['phone'],
                'priority_score' => $data['priority'],
                'provider_routing' => $data['routing'],
            ]);

            EmergencyContact::create([
                'emergency_center_id' => $center->id,
                'operator' => Operator::CELTIIS,
                'phone_number' => $data['phone'],
                'priority_score' => $data['priority'],
                'provider_routing' => $data['routing'],
            ]);
        }
    }

    private function seedRegionalCenters(): void
    {
        $centers = $this->getRegionalData();

        $departmentCache = [];

        foreach ($centers as $entry) {
            $deptCode = Str::slug($entry['department']);

            if (!isset($departmentCache[$deptCode])) {
                $departmentCache[$deptCode] = Department::firstOrCreate(
                    ['code' => $deptCode],
                    ['name' => $entry['department']],
                );
            }

            $department = $departmentCache[$deptCode];

            $center = EmergencyCenter::create([
                'department_id' => $department->id,
                'name' => $entry['commune'],
                'slug' => Str::slug('ccpc-' . $entry['commune']),
                'type' => EmergencyCenterType::CCPC,
                'category' => EmergencyCategory::CIVIL_PROTECTION,
            ]);

            foreach ($entry['contacts'] as $contact) {
                EmergencyContact::create([
                    'emergency_center_id' => $center->id,
                    'operator' => $contact['operator'],
                    'phone_number' => $contact['phone'],
                    'priority_score' => $contact['priority'],
                    'provider_routing' => 'local_ccpc',
                ]);
            }
        }
    }

    /** @return array<int, array{department: string, commune: string, contacts: array<int, array{operator: Operator, phone: string, priority: int}>}> */
    private function getRegionalData(): array
    {
        return [
            $this->center('Alibori', 'Banikoara', '+2290145100202', '+2290151232584', '+2290149107688'),
            $this->center('Alibori', 'Malanville', '+2290145100303', '+2290151176372', '+2290149107697'),
            $this->center('Alibori', 'Kandi', '+2290145100404', '+2290151145950', '+2290149672166'),
            $this->center('Atacora', 'Tanguieta', '+2290145100505', '+2290151141260', '+2290149672370'),
            $this->center('Atacora', 'Natitingou', '+2290145100606', '+2290151142469', '+2290149679901'),
            $this->center('Atlantique', 'Allada', '+2290145102121', '+2290151107947', '+2290192759248'),
            $this->center('Atlantique', 'Ouidah', '+2290145102222', '+2290152648978', '+2290192759475'),
            $this->center('Atlantique', 'Abomey-Calavi', '+2290145102323', '+2290152940683', '+2290192759476'),
            $this->center('Atlantique', 'Cococodji', '+2290145102424', '+2290152587698', '+2290192759541'),
            $this->center('Atlantique', 'Ouidah (GDIZ)', '+2290145102525', '+2290152594129', '+2290140580629'),
            $this->center('Borgou', 'Parakou', '+2290145100707', '+2290152980133', '+2290143817702'),
            $this->center('Collines', 'Dassa', '+2290145101111', '+2290152875972', '+2290143817828'),
            $this->center('Collines', 'Savalou', '+2290145101212', '+2290152567668', '+2290143817855'),
            $this->center('Donga', 'Djougou', '+2290145100808', '+2290152590036', '+2290143817933'),
            $this->center('Donga', 'Bassila', '+2290145100909', '+2290152641194', '+2290143814172'),
            $this->center('Littoral', 'Cotonou St-Jean', '+2290145101818', '+2290152412299', '+2290143818419'),
            $this->center('Littoral', 'Cotonou Sodjatime', '+2290145101919', '+2290152654582', '+2290143820477'),
            $this->center('Littoral', 'Cotonou Tokpa', '+2290145102020', '+2290152571017', '+2290143821445'),
            $this->center('Mono-Couffo', 'Lokossa', '+2290145102626', '+2290152707507', '+2290143921428'),
            $this->center('Mono-Couffo', 'Dogbo', '+2290145102727', '+2290152550793', '+2290143821525'),
            $this->center('Mono-Couffo', 'Come', '+2290145102828', '+2290152590037', '+2290143824254'),
            $this->center('Oueme-Plateau', 'Porto-Novo', '+2290145101515', '+2290152565245', '+2290143824548'),
            $this->center('Oueme-Plateau', 'Seme-Krake', '+2290145101616', '+2290152735097', '+2290143823331'),
            $this->center('Oueme-Plateau', 'Pobe', '+2290145101717', '+2290152713254', '+2290143825242'),
            $this->center('Zou', 'Bohicon', '+2290145101313', '+2290152736472', '+2290143825242'),
            $this->center('Zou', 'Cove', '+2290145101414', '+2290152888304', '+2290143825262'),
        ];
    }

    /** @return array{department: string, commune: string, contacts: array<int, array{operator: Operator, phone: string, priority: int}>} */
    private function center(string $department, string $commune, string $moov, string $mtn, string $celtiis): array
    {
        return [
            'department' => $department,
            'commune' => $commune,
            'contacts' => [
                ['operator' => Operator::MOOV, 'phone' => $this->formatPhone($moov), 'priority' => 50],
                ['operator' => Operator::MTN, 'phone' => $this->formatPhone($mtn), 'priority' => 50],
                ['operator' => Operator::CELTIIS, 'phone' => $this->formatPhone($celtiis), 'priority' => 50],
            ],
        ];
    }

    private function formatPhone(string $raw): string
    {
        return preg_replace('/\s+/', '', $raw) ?? $raw;
    }
}
