<?php

declare(strict_types=1);

namespace App\Modules\Vault\DTOs;

final readonly class HealthData
{
    /**
     * @param array<string> $allergies
     * @param array<string> $medications
     * @param array<string> $conditions
     */
    public function __construct(
        public ?string $bloodType = null,
        public array $allergies = [],
        public array $medications = [],
        public array $conditions = [],
        public ?string $emergencyNotes = null,
        public ?float $weightKg = null,
        public ?float $heightCm = null,
    ) {}

    public function toJson(): string
    {
        return (string) json_encode([
            'blood_type' => $this->bloodType,
            'allergies' => $this->allergies,
            'medications' => $this->medications,
            'conditions' => $this->conditions,
            'emergency_notes' => $this->emergencyNotes,
            'weight_kg' => $this->weightKg,
            'height_cm' => $this->heightCm,
        ], JSON_THROW_ON_ERROR);
    }

    public static function fromJson(string $json): self
    {
        /** @var array{blood_type: ?string, allergies: array<string>, medications: array<string>, conditions: array<string>, emergency_notes: ?string, weight_kg: ?float, height_cm: ?float} $data */
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return new self(
            bloodType: $data['blood_type'] ?? null,
            allergies: $data['allergies'] ?? [],
            medications: $data['medications'] ?? [],
            conditions: $data['conditions'] ?? [],
            emergencyNotes: $data['emergency_notes'] ?? null,
            weightKg: isset($data['weight_kg']) ? (float) $data['weight_kg'] : null,
            heightCm: isset($data['height_cm']) ? (float) $data['height_cm'] : null,
        );
    }
}
