<?php

declare(strict_types=1);

namespace App\Modules\Vault\Resources;

use App\Modules\Vault\DTOs\HealthData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class DecryptedHealthDataResource extends JsonResource
{
    /** @param HealthData $resource */
    public function __construct(
        private readonly HealthData $healthData,
    ) {
        parent::__construct($healthData);
    }

    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'blood_type' => $this->healthData->bloodType,
            'allergies' => $this->healthData->allergies,
            'medications' => $this->healthData->medications,
            'conditions' => $this->healthData->conditions,
            'emergency_notes' => $this->healthData->emergencyNotes,
            'weight_kg' => $this->healthData->weightKg,
            'height_cm' => $this->healthData->heightCm,
        ];
    }
}
