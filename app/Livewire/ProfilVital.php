<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Livewire\Concerns\RequiresAuth;
use App\Modules\Vault\DTOs\HealthData;
use App\Modules\Vault\Models\Vault;
use App\Modules\Vault\Commands\StoreHealthData\StoreHealthDataCommand;
use App\Modules\Vault\Queries\GetDecryptedHealthData\GetDecryptedHealthDataQuery;
use App\Shared\Bus\CommandBus;
use App\Shared\Bus\QueryBus;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Profil Vital - Lafia')]
final class ProfilVital extends Component
{
    use RequiresAuth;
    public bool $isEditing = false;
    public bool $isLocked = true;
    public ?string $vaultId = null;

    public string $bloodType = '';
    public string $emergencyNotes = '';
    public ?float $weightKg = null;
    public ?float $heightCm = null;

    /** @var array<string> */
    public array $allergies = [];
    /** @var array<string> */
    public array $medications = [];
    /** @var array<string> */
    public array $conditions = [];

    public string $newAllergy = '';
    public string $newMedication = '';
    public string $newCondition = '';

    public ?array $decryptedData = null;

    public function mount(): void
    {
        $vault = Vault::where('user_id', 1)
            ->where('data_type', \App\Modules\Vault\Enums\VaultDataType::HEALTH)
            ->latest()
            ->first();

        if ($vault) {
            $this->vaultId = $vault->id;
        }
    }

    public function unlock(): void
    {
        if (!$this->vaultId) {
            $this->isLocked = false;
            $this->isEditing = true;
            return;
        }

        $queryBus = app(QueryBus::class);
        $healthData = $queryBus->dispatch(new GetDecryptedHealthDataQuery(
            vaultId: $this->vaultId,
            userId: 1,
        ));

        $this->decryptedData = [
            'blood_type' => $healthData->bloodType,
            'allergies' => $healthData->allergies,
            'medications' => $healthData->medications,
            'conditions' => $healthData->conditions,
            'emergency_notes' => $healthData->emergencyNotes,
            'weight_kg' => $healthData->weightKg,
            'height_cm' => $healthData->heightCm,
        ];

        $this->bloodType = $healthData->bloodType ?? '';
        $this->allergies = $healthData->allergies;
        $this->medications = $healthData->medications;
        $this->conditions = $healthData->conditions;
        $this->emergencyNotes = $healthData->emergencyNotes ?? '';
        $this->weightKg = $healthData->weightKg;
        $this->heightCm = $healthData->heightCm;

        $this->isLocked = false;
    }

    public function edit(): void
    {
        $this->isEditing = true;
    }

    public function addAllergy(): void
    {
        $value = trim($this->newAllergy);
        if ($value !== '' && !in_array($value, $this->allergies, true)) {
            $this->allergies[] = $value;
        }
        $this->newAllergy = '';
    }

    public function removeAllergy(int $index): void
    {
        unset($this->allergies[$index]);
        $this->allergies = array_values($this->allergies);
    }

    public function addMedication(): void
    {
        $value = trim($this->newMedication);
        if ($value !== '' && !in_array($value, $this->medications, true)) {
            $this->medications[] = $value;
        }
        $this->newMedication = '';
    }

    public function removeMedication(int $index): void
    {
        unset($this->medications[$index]);
        $this->medications = array_values($this->medications);
    }

    public function addCondition(): void
    {
        $value = trim($this->newCondition);
        if ($value !== '' && !in_array($value, $this->conditions, true)) {
            $this->conditions[] = $value;
        }
        $this->newCondition = '';
    }

    public function removeCondition(int $index): void
    {
        unset($this->conditions[$index]);
        $this->conditions = array_values($this->conditions);
    }

    public function save(): void
    {
        $commandBus = app(CommandBus::class);

        $commandBus->dispatch(new StoreHealthDataCommand(
            userId: 1,
            label: 'Mon dossier sante',
            healthData: new HealthData(
                bloodType: $this->bloodType ?: null,
                allergies: $this->allergies,
                medications: $this->medications,
                conditions: $this->conditions,
                emergencyNotes: $this->emergencyNotes ?: null,
                weightKg: $this->weightKg,
                heightCm: $this->heightCm,
            ),
        ));

        $vault = Vault::where('user_id', 1)
            ->where('data_type', \App\Modules\Vault\Enums\VaultDataType::HEALTH)
            ->latest()
            ->first();

        $this->vaultId = $vault?->id;
        $this->isEditing = false;
        $this->unlock();
    }

    public function lock(): void
    {
        $this->isLocked = true;
        $this->decryptedData = null;
        $this->isEditing = false;
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.profil-vital');
    }
}
