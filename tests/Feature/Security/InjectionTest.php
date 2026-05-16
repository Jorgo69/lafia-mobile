<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Modules\Emergency\Models\Department;
use App\Modules\Emergency\Models\EmergencyCenter;
use App\Modules\Emergency\Models\EmergencyContact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesTestUser;
use Tests\TestCase;

final class InjectionTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestUser;

    private function seedData(): EmergencyContact
    {
        $dept = Department::create(['name' => 'Littoral', 'code' => 'littoral']);
        $center = EmergencyCenter::create([
            'department_id' => $dept->id, 'name' => 'Test', 'slug' => 'test',
            'type' => 'ccpc', 'category' => 'civil_protection', 'is_active' => true,
        ]);
        return EmergencyContact::create([
            'emergency_center_id' => $center->id, 'operator' => 'mtn',
            'phone_number' => '+22901512325', 'priority_score' => 80,
            'provider_routing' => 'local_ccpc', 'is_active' => true,
        ]);
    }

    public function test_sql_injection_in_operator_param(): void
    {
        $this->getJson('/api/emergency/centers/operator/mtn\' OR 1=1--')->assertStatus(422);
    }

    public function test_sql_injection_in_coordinates(): void
    {
        $this->getJson('/api/emergency/centers/nearest?lat=6.3; DROP TABLE departments;--&lng=2.4')
            ->assertStatus(422);
    }

    public function test_sql_injection_in_report_does_not_delete_data(): void
    {
        $contact = $this->seedData();

        $this->postJson('/api/emergency/report', [
            'emergency_contact_id' => $contact->id,
            'reported_issue' => "not_responding'; DROP TABLE emergency_contacts;--",
        ]);

        $this->assertTrue(EmergencyContact::where('id', $contact->id)->exists());
    }

    public function test_xss_in_vault(): void
    {
        $this->createTestUser();

        $response = $this->postJson('/api/vault/health', [
            'label' => '<script>alert(1)</script>',
            'blood_type' => 'O+',
        ]);

        $response->assertStatus(201);
        $this->assertIsString($response->json('data.label'));
    }

    public function test_api_routes_are_stateless(): void
    {
        $this->getJson('/api/emergency/centers')->assertOk();
    }
}
