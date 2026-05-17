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

    public Operator $userOperator = Operator::MTN;
    public string $selectedCategory = '';

    /** @var array<int, array<string, mixed>> */
    public array $nationalServices = [];

    /** @var array<int, array<string, mixed>> */
    public array $localCenters = [];

    public function mount(): void
    {
        $this->userOperator = Operator::tryFrom(app(SettingsService::class)->get('dashboard_operator', 'mtn') ?? 'mtn') ?? Operator::MTN;
        $this->loadNationalServices();
        $this->loadLocalCenters();
    }

    public function callNumber(string $phoneNumber): void
    {
        $mode = app(SettingsService::class)->get('call_mode', 'dial');
        $uri = ($mode === 'direct' ? 'tel-direct:' : 'tel:') . $phoneNumber;
        $this->dispatch('initiate-call', uri: $uri);
    }

    public function setOperator(string $operator): void
    {
        $op = Operator::tryFrom($operator);
        if ($op === null) {
            return;
        }
        $this->userOperator = $op;
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
            'short_code' => $center->contacts->first()?->phone_number ?? $center->category->shortCode(),
            'phone' => $center->contacts->first()?->phone_number ?? '',
        ])->toArray();
    }

    private function loadLocalCenters(): void
    {
        $this->localCenters = EmergencyCenter::with(['contacts' => function ($q): void {
            $q->where('operator', $this->userOperator)->where('is_active', true);
        }, 'department'])
            ->where('type', EmergencyCenterType::CCPC)
            ->where('is_active', true)
            ->orderBy('name')
            ->limit(10)
            ->get()
            ->map(fn (EmergencyCenter $center) => [
                'id'         => $center->id,
                'name'       => $center->name,
                'department' => $center->department->name,
                'phone'      => $center->contacts->first()?->phone_number ?? 'N/A',
                'operator'   => $this->userOperator->label(),
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
        return view('livewire.dashboard', [
            'operators' => Operator::cases(),
        ]);
    }
}
