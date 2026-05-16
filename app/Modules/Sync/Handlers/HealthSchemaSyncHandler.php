<?php

declare(strict_types=1);

namespace App\Modules\Sync\Handlers;

use App\Modules\Sync\Models\DynamicSchema;
use App\Modules\Sync\Services\SyncHandlerInterface;
use Illuminate\Support\Facades\DB;

final class HealthSchemaSyncHandler implements SyncHandlerInterface
{
    /**
     * Expected data format:
     * {
     *   "version": "2.0",
     *   "fields": [
     *     {"key": "blood_type", "label": "Groupe sanguin", "type": "select", "required": true, "options": ["A+","A-","B+","B-","AB+","AB-","O+","O-"]},
     *     {"key": "allergies", "label": "Allergies", "type": "multi", "required": false},
     *     {"key": "national_id", "label": "Numero CNI Biometrique", "type": "text", "required": false},
     *   ]
     * }
     */
    public function apply(array $data): int
    {
        return DB::transaction(function () use ($data): int {
            $fields = $data['fields'] ?? [];
            $version = $data['version'] ?? '1.0';

            DynamicSchema::updateOrCreate(
                ['scope' => 'health'],
                [
                    'fields' => $fields,
                    'version' => $version,
                ],
            );

            return count($fields);
        });
    }

    public function snapshot(): array
    {
        $schema = DynamicSchema::where('scope', 'health')->first();

        if ($schema === null) {
            return [];
        }

        return [
            'version' => $schema->version,
            'fields' => $schema->fields,
        ];
    }

    public function restore(array $data): int
    {
        if (empty($data)) {
            return 0;
        }

        return DB::transaction(function () use ($data): int {
            DynamicSchema::updateOrCreate(
                ['scope' => 'health'],
                [
                    'fields' => $data['fields'] ?? [],
                    'version' => $data['version'] ?? '1.0',
                ],
            );

            return count($data['fields'] ?? []);
        });
    }
}
