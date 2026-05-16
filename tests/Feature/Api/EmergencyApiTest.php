<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Modules\Emergency\Models\Department;
use App\Modules\Emergency\Models\EmergencyCenter;
use App\Modules\Emergency\Models\EmergencyContact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class EmergencyApiTest extends TestCase
{
    use RefreshDatabase;

    private function seedData(): EmergencyContact
    {
        $dept = Department::create(['name' => 'Littoral', 'code' => 'littoral']);
        $center = EmergencyCenter::create([
            'department_id' => $dept->id,
            'name' => 'Cotonou Centre',
            'slug' => 'ccpc-cotonou-centre',
            'type' => 'ccpc',
            'category' => 'civil_protection',
            'latitude' => 6.3654,
            'longitude' => 2.4183,
            'is_active' => true,
        ]);
        return EmergencyContact::create([
            'emergency_center_id' => $center->id,
            'operator' => 'mtn',
            'phone_number' => '+22901512325',
            'priority_score' => 80,
            'provider_routing' => 'local_ccpc',
            'is_active' => true,
        ]);
    }

    public function test_get_all_centers(): void
    {
        $this->seedData();
        $this->getJson('/api/emergency/centers')->assertOk();
    }

    public function test_get_contacts_by_operator(): void
    {
        $this->seedData();
        $this->getJson('/api/emergency/centers/operator/mtn')->assertOk();
    }

    public function test_get_contacts_by_invalid_operator(): void
    {
        $this->getJson('/api/emergency/centers/operator/orange')->assertStatus(422);
    }

    public function test_get_nearest_center(): void
    {
        $this->seedData();
        $this->getJson('/api/emergency/centers/nearest?lat=6.3654&lng=2.4183')->assertOk();
    }

    public function test_get_nearest_without_coords_fails(): void
    {
        $this->getJson('/api/emergency/centers/nearest')->assertStatus(422);
    }

    public function test_report_service_update(): void
    {
        $contact = $this->seedData();
        $this->postJson('/api/emergency/report', [
            'emergency_contact_id' => $contact->id,
            'reported_issue' => 'Le numero ne repond pas depuis 2 heures',
        ])->assertStatus(201);
    }

    public function test_report_without_issue_fails(): void
    {
        $contact = $this->seedData();
        $this->postJson('/api/emergency/report', [
            'emergency_contact_id' => $contact->id,
        ])->assertStatus(422);
    }
}
