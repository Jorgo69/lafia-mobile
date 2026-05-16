<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\Dashboard;
use App\Livewire\PharmacieGarde;
use App\Livewire\UssdGuide;
use App\Services\Crypto\KeyPairManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

final class PagesRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_renders(): void
    {
        $this->get('/')->assertOk()->assertSee('SOS');
    }

    public function test_ussd_guide_renders(): void
    {
        $this->get('/ussd')->assertOk();
    }

    public function test_pharmacie_garde_renders(): void
    {
        $this->get('/pharmacies')->assertOk();
    }

    public function test_profil_vital_renders(): void
    {
        $this->get('/profil-vital')->assertOk();
    }

    public function test_cercle_confiance_renders(): void
    {
        $this->get('/cercle')->assertOk();
    }

    public function test_dashboard_livewire(): void
    {
        Livewire::test(Dashboard::class)->assertStatus(200);
    }

    public function test_pharmacie_garde_zone_filter(): void
    {
        Livewire::test(PharmacieGarde::class)
            ->call('setZone', 'littoral')
            ->assertSet('activeZone', 'littoral');
    }

    public function test_pharmacie_garde_view_modes(): void
    {
        Livewire::test(PharmacieGarde::class)
            ->call('setViewMode', 'all')
            ->assertSet('viewMode', 'all');
    }

    public function test_404_on_unknown_route(): void
    {
        $this->get('/nonexistent-page')->assertStatus(404);
    }
}
