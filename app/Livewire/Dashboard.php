<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Livewire\Concerns\HandlesReports;
use App\Modules\Emergency\Enums\EmergencyCategory;
use App\Modules\Emergency\Enums\EmergencyCenterType;
use App\Modules\Tips\Models\PracticalTip;
use App\Services\Settings\SettingsService;
use App\Shared\Enums\Operator;
use App\Modules\Emergency\Models\EmergencyCenter;
use App\Modules\Emergency\Models\EmergencyContact;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('SOS - Lafia')]
final class Dashboard extends Component
{
    use HandlesReports;

    public string $userOperator = 'mtn';
    public string $selectedCategory = '';

    /** @var array<int, array<string, mixed>> */
    public array $nationalServices = [];

    /** @var array<int, array<string, mixed>> */
    public array $localCenters = [];

    public function mount(): void
    {
        $this->userOperator = app(SettingsService::class)->get('dashboard_operator', 'mtn') ?? 'mtn';
        $this->loadNationalServices();
        $this->loadLocalCenters();
    }

    public function callNumber(string $phoneNumber): void
    {
        $this->dispatch('initiate-call', number: $phoneNumber);
    }

    public function setOperator(string $operator): void
    {
        $this->userOperator = $operator;
        $this->loadLocalCenters();
        app(SettingsService::class)->set('dashboard_operator', $operator);
    }

    public function filterByCategory(string $category): void
    {
        $this->selectedCategory = ($category === '' || $this->selectedCategory === $category) ? '' : $category;
        $this->loadNationalServices();
    }

    private function loadNationalServices(): void
    {
        $query = EmergencyCenter::with('contacts')
            ->where('type', EmergencyCenterType::NATIONAL)
            ->where('is_active', true);

        if ($this->selectedCategory !== '') {
            $query->where('category', $this->selectedCategory);
        }

        $this->nationalServices = $query->orderByDesc(
            EmergencyContact::select('priority_score')
                ->whereColumn('emergency_center_id', 'emergency_centers.id')
                ->orderByDesc('priority_score')
                ->limit(1)
        )->get()->map(fn (EmergencyCenter $center) => [
            'id' => $center->id,
            'name' => $center->name,
            'category' => $center->category->value,
            'category_label' => $center->category->label(),
            'short_code' => $center->category->shortCode(),
            'phone' => $center->contacts->first()?->phone_number ?? '',
        ])->toArray();
    }

    private function loadLocalCenters(): void
    {
        $operator = Operator::tryFrom($this->userOperator) ?? Operator::MTN;

        $this->localCenters = EmergencyCenter::with(['contacts' => function ($q) use ($operator) {
            $q->where('operator', $operator)->where('is_active', true);
        }, 'department'])
            ->where('type', EmergencyCenterType::CCPC)
            ->where('is_active', true)
            ->orderBy('name')
            ->limit(10)
            ->get()
            ->map(fn (EmergencyCenter $center) => [
                'id' => $center->id,
                'name' => $center->name,
                'department' => $center->department->name,
                'phone' => $center->contacts->first()?->phone_number ?? 'N/A',
                'operator' => $operator->label(),
            ])->toArray();
    }

    /** @return \Illuminate\Support\Collection<int, PracticalTip> */
    public function getTipsProperty(): \Illuminate\Support\Collection
    {
        return PracticalTip::active()
            ->orderByDesc('is_pinned')
            ->orderBy('sort_order')
            ->limit(5)
            ->get();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.dashboard');
    }
}
