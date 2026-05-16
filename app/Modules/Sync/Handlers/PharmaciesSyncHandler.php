<?php

declare(strict_types=1);

namespace App\Modules\Sync\Handlers;

use App\Modules\Pharmacy\Models\Pharmacy;
use App\Modules\Sync\Services\SyncHandlerInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class PharmaciesSyncHandler implements SyncHandlerInterface
{
    public function apply(array $data): int
    {
        $updated = 0;

        DB::transaction(function () use ($data, &$updated) {
            foreach ($data as $entry) {
                $slug = Str::slug($entry['name']);

                $pharmacy = Pharmacy::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => $entry['name'],
                        'zone' => $entry['zone'],
                        'city' => $entry['city'],
                        'neighborhood' => $entry['neighborhood'] ?? null,
                        'phone' => $entry['phone'] ?? null,
                        'latitude' => $entry['lat'] ?? $entry['latitude'] ?? null,
                        'longitude' => $entry['lng'] ?? $entry['longitude'] ?? null,
                        'is_active' => $entry['is_active'] ?? true,
                    ],
                );

                if ($pharmacy->wasRecentlyCreated || $pharmacy->wasChanged()) {
                    $updated++;
                }
            }
        });

        return $updated;
    }

    public function snapshot(): array
    {
        return Pharmacy::where('is_active', true)
            ->get()
            ->map(fn (Pharmacy $p) => [
                'name' => $p->name,
                'zone' => $p->zone,
                'city' => $p->city,
                'neighborhood' => $p->neighborhood,
                'phone' => $p->phone,
                'latitude' => $p->latitude,
                'longitude' => $p->longitude,
                'is_active' => $p->is_active,
            ])
            ->toArray();
    }

    public function restore(array $data): int
    {
        $restored = 0;

        DB::transaction(function () use ($data, &$restored) {
            Pharmacy::query()->delete();

            foreach ($data as $entry) {
                Pharmacy::create([
                    'slug' => Str::slug($entry['name']),
                    'name' => $entry['name'],
                    'zone' => $entry['zone'],
                    'city' => $entry['city'],
                    'neighborhood' => $entry['neighborhood'] ?? null,
                    'phone' => $entry['phone'] ?? null,
                    'latitude' => $entry['latitude'] ?? null,
                    'longitude' => $entry['longitude'] ?? null,
                    'is_active' => $entry['is_active'] ?? true,
                ]);
                $restored++;
            }
        });

        return $restored;
    }
}
