<?php

declare(strict_types=1);

namespace App\Modules\Sync\Services;

use App\Modules\Sync\Models\DynamicSchema;

final class DynamicSchemaService
{
    /** @var array<string, DynamicSchema|null> */
    private static array $cache = [];

    /**
     * @return array<int, array{key: string, label: string, type: string, required: bool, options?: array<string>}>
     */
    public function getFields(string $scope): array
    {
        $schema = $this->getSchema($scope);

        if ($schema === null) {
            return $this->getDefaultFields($scope);
        }

        return $schema->getFieldDefinitions();
    }

    public function getVersion(string $scope): string
    {
        return $this->getSchema($scope)?->version ?? '1.0';
    }

    private function getSchema(string $scope): ?DynamicSchema
    {
        if (!array_key_exists($scope, self::$cache)) {
            self::$cache[$scope] = DynamicSchema::find($scope);
        }

        return self::$cache[$scope];
    }

    /**
     * @return array<int, array{key: string, label: string, type: string, required: bool, options?: array<string>}>
     */
    private function getDefaultFields(string $scope): array
    {
        return match ($scope) {
            'health' => [
                ['key' => 'blood_type', 'label' => 'Groupe sanguin', 'type' => 'select', 'required' => false, 'options' => ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']],
                ['key' => 'weight_kg', 'label' => 'Poids (kg)', 'type' => 'number', 'required' => false],
                ['key' => 'height_cm', 'label' => 'Taille (cm)', 'type' => 'number', 'required' => false],
                ['key' => 'allergies', 'label' => 'Allergies', 'type' => 'multi', 'required' => false],
                ['key' => 'medications', 'label' => 'Medicaments', 'type' => 'multi', 'required' => false],
                ['key' => 'conditions', 'label' => 'Conditions medicales', 'type' => 'multi', 'required' => false],
                ['key' => 'emergency_notes', 'label' => 'Notes d\'urgence', 'type' => 'textarea', 'required' => false],
            ],
            default => [],
        };
    }
}
