<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Livewire\Concerns\HandlesReports;
use App\Livewire\Enums\PharmacyViewMode;
use App\Modules\Pharmacy\Enums\PharmacyZone;
use App\Modules\Pharmacy\Models\Pharmacy;
use App\Modules\Pharmacy\Models\PharmacyGuard;
use App\Services\Settings\SettingsService;
use Illuminate\Support\Collection;
use Livewire\Component;

final class PharmacieGarde extends Component
{
    use HandlesReports;
    public string $activeZone = 'littoral';
    public PharmacyViewMode $viewMode = PharmacyViewMode::GUARD;
    public ?float $userLat = null;
    public ?float $userLng = null;
    public string $searchQuery = '';

    public function mount(): void
    {
        $settings = app(SettingsService::class);
        $this->activeZone = $settings->get('pharma_zone', 'littoral') ?? 'littoral';
        $this->viewMode = PharmacyViewMode::tryFrom($settings->get('pharma_view', 'guard') ?? 'guard') ?? PharmacyViewMode::GUARD;
    }

    public function setZone(string $zone): void
    {
        $this->activeZone = $zone;
        app(SettingsService::class)->set('pharma_zone', $zone);
    }

    public function setViewMode(string $mode): void
    {
        $m = PharmacyViewMode::tryFrom($mode);
        if ($m === null) {
            return;
        }
        $this->viewMode = $m;
        app(SettingsService::class)->set('pharma_view', $mode);
    }

    public function setUserLocation(float $lat, float $lng): void
    {
        $this->userLat = $lat;
        $this->userLng = $lng;
        $this->viewMode = PharmacyViewMode::NEAREST;
    }

    /**
     * Pharmacies de garde cette semaine dans la zone active.
     *
     * @return Collection<int, Pharmacy>
     */
    public function getOnGuardProperty(): Collection
    {
        $today = now()->toDateString();

        return Pharmacy::where('zone', $this->activeZone)
            ->where('is_active', true)
            ->whereHas('guards', fn ($q) => $q
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today))
            ->orderBy('name')
            ->get();
    }

    /**
     * Toutes les pharmacies de la zone.
     *
     * @return Collection<int, Pharmacy>
     */
    public function getAllPharmaciesProperty(): Collection
    {
        $query = Pharmacy::where('zone', $this->activeZone)
            ->where('is_active', true);

        if ($this->searchQuery !== '') {
            $search = $this->searchQuery;
            $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('neighborhood', 'like', "%{$search}%"));
        }

        return $query->orderBy('name')->get();
    }

    /**
     * Pharmacies de garde les plus proches.
     *
     * @return Collection<int, array{pharmacy: Pharmacy, distance: float}>
     */
    public function getNearestProperty(): Collection
    {
        if ($this->userLat === null || $this->userLng === null) {
            return collect();
        }

        $today = now()->toDateString();

        return Pharmacy::where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereHas('guards', fn ($q) => $q
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today))
            ->get()
            ->map(fn (Pharmacy $p) => [
                'pharmacy' => $p,
                'distance' => $p->distanceFrom($this->userLat, $this->userLng),
            ])
            ->sortBy('distance')
            ->take(5);
    }

    /**
     * @return Collection<int, Pharmacy>
     */
    public function getNextWeekGuardsProperty(): Collection
    {
        $nextWeekStart = now()->addWeek()->startOfWeek()->toDateString();
        $nextWeekEnd = now()->addWeek()->endOfWeek()->toDateString();

        return Pharmacy::where('zone', $this->activeZone)
            ->where('is_active', true)
            ->whereHas('guards', fn ($q) => $q
                ->where('start_date', '<=', $nextWeekEnd)
                ->where('end_date', '>=', $nextWeekStart))
            ->orderBy('name')
            ->get();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.pharmacie-garde', [
            'zones'     => PharmacyZone::cases(),
            'viewModes' => PharmacyViewMode::cases(),
        ])->layout('components.layouts.app', ['title' => __('pharma.title')]);
    }
}
