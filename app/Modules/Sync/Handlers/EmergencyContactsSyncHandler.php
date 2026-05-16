<?php

declare(strict_types=1);

namespace App\Modules\Sync\Handlers;

use App\Modules\Emergency\Models\Department;
use App\Modules\Emergency\Models\EmergencyCenter;
use App\Modules\Emergency\Models\EmergencyContact;
use App\Modules\Emergency\Models\EmergencyServiceUpdate;
use App\Modules\Sync\Services\SyncHandlerInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class EmergencyContactsSyncHandler implements SyncHandlerInterface
{
    /**
     * Expected data format:
     * [
     *   {
     *     "department": "Alibori",
     *     "center": "Banikoara",
     *     "type": "ccpc",
     *     "category": "civil_protection",
     *     "contacts": [
     *       {"operator": "mtn", "phone": "+2290151232584"},
     *       {"operator": "moov", "phone": "+2290145100202"},
     *     ],
     *     "latitude": 11.3000,
     *     "longitude": 2.4333,
     *   }
     * ]
     */
    public function apply(array $data): int
    {
        $updated = 0;

        DB::transaction(function () use ($data, &$updated) {
            foreach ($data as $entry) {
                $department = Department::firstOrCreate(
                    ['code' => Str::slug($entry['department'])],
                    ['name' => $entry['department']],
                );

                $slug = Str::slug(($entry['type'] ?? 'ccpc') . '-' . $entry['center']);

                $center = EmergencyCenter::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'department_id' => $department->id,
                        'name' => $entry['center'],
                        'type' => $entry['type'] ?? 'ccpc',
                        'category' => $entry['category'] ?? 'civil_protection',
                        'latitude' => $entry['latitude'] ?? null,
                        'longitude' => $entry['longitude'] ?? null,
                        'is_active' => true,
                    ],
                );

                foreach ($entry['contacts'] ?? [] as $contact) {
                    $existing = EmergencyContact::where('emergency_center_id', $center->id)
                        ->where('operator', $contact['operator'])
                        ->first();

                    if ($existing) {
                        // Only update if no pending crowdsource reports
                        $hasPendingReports = EmergencyServiceUpdate::where('emergency_contact_id', $existing->id)
                            ->where('status', \App\Modules\Emergency\Enums\UpdateStatus::PENDING)
                            ->exists();

                        if (!$hasPendingReports && $existing->phone_number !== $contact['phone']) {
                            $existing->update([
                                'phone_number' => $contact['phone'],
                                'is_active' => $contact['is_active'] ?? true,
                            ]);
                            $updated++;
                        }
                    } else {
                        EmergencyContact::create([
                            'emergency_center_id' => $center->id,
                            'operator' => $contact['operator'],
                            'phone_number' => $contact['phone'],
                            'priority_score' => $contact['priority_score'] ?? 50,
                            'provider_routing' => $contact['provider_routing'] ?? 'local_ccpc',
                            'is_active' => $contact['is_active'] ?? true,
                        ]);
                        $updated++;
                    }
                }
            }
        });

        return $updated;
    }

    public function snapshot(): array
    {
        return EmergencyCenter::with(['contacts', 'department'])
            ->where('is_active', true)
            ->get()
            ->map(fn (EmergencyCenter $center) => [
                'department' => $center->department->name,
                'center' => $center->name,
                'type' => $center->type,
                'category' => $center->category->value ?? $center->category,
                'latitude' => $center->latitude,
                'longitude' => $center->longitude,
                'contacts' => $center->contacts->map(fn (EmergencyContact $c) => [
                    'operator' => $c->operator->value ?? $c->operator,
                    'phone' => $c->phone_number,
                    'priority_score' => $c->priority_score,
                    'provider_routing' => $c->provider_routing,
                    'is_active' => $c->is_active,
                ])->toArray(),
            ])
            ->toArray();
    }

    public function restore(array $data): int
    {
        $restored = 0;

        DB::transaction(function () use ($data, &$restored) {
            EmergencyContact::query()->delete();
            EmergencyCenter::query()->delete();

            foreach ($data as $entry) {
                $department = Department::firstOrCreate(
                    ['code' => Str::slug($entry['department'])],
                    ['name' => $entry['department']],
                );

                $slug = Str::slug(($entry['type'] ?? 'ccpc') . '-' . $entry['center']);

                $center = EmergencyCenter::create([
                    'slug' => $slug,
                    'department_id' => $department->id,
                    'name' => $entry['center'],
                    'type' => $entry['type'] ?? 'ccpc',
                    'category' => $entry['category'] ?? 'civil_protection',
                    'latitude' => $entry['latitude'] ?? null,
                    'longitude' => $entry['longitude'] ?? null,
                    'is_active' => true,
                ]);

                foreach ($entry['contacts'] ?? [] as $contact) {
                    EmergencyContact::create([
                        'emergency_center_id' => $center->id,
                        'operator' => $contact['operator'],
                        'phone_number' => $contact['phone'],
                        'priority_score' => $contact['priority_score'] ?? 50,
                        'provider_routing' => $contact['provider_routing'] ?? 'local_ccpc',
                        'is_active' => $contact['is_active'] ?? true,
                    ]);
                    $restored++;
                }
            }
        });

        return $restored;
    }
}
