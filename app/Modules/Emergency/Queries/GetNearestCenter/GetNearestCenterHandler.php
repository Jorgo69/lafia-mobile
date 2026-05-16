<?php

declare(strict_types=1);

namespace App\Modules\Emergency\Queries\GetNearestCenter;

use App\Modules\Emergency\Models\EmergencyCenter;
use App\Shared\Bus\Query;
use App\Shared\Bus\QueryHandler;
use Illuminate\Database\Eloquent\Collection;

final class GetNearestCenterHandler implements QueryHandler
{
    /**
     * Haversine approximation via SQLite.
     * Precision suffisante pour du routing d'urgence a l'echelle du Benin.
     */
    public function handle(Query $query): Collection
    {
        assert($query instanceof GetNearestCenterQuery);

        $lat = $query->latitude;
        $lng = $query->longitude;

        $builder = EmergencyCenter::with(['department', 'contacts'])
            ->where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->selectRaw('*, (
                6371 * 2 * atan2(
                    sqrt(
                        sin(radians(latitude - ?) / 2) * sin(radians(latitude - ?) / 2) +
                        cos(radians(?)) * cos(radians(latitude)) *
                        sin(radians(longitude - ?) / 2) * sin(radians(longitude - ?) / 2)
                    ),
                    sqrt(1 - (
                        sin(radians(latitude - ?) / 2) * sin(radians(latitude - ?) / 2) +
                        cos(radians(?)) * cos(radians(latitude)) *
                        sin(radians(longitude - ?) / 2) * sin(radians(longitude - ?) / 2)
                    ))
                )
            ) AS distance_km', [$lat, $lat, $lat, $lng, $lng, $lat, $lat, $lat, $lng, $lng]);

        if ($query->category !== null) {
            $builder->where('category', $query->category);
        }

        return $builder
            ->orderBy('distance_km')
            ->limit($query->limit)
            ->get();
    }
}
