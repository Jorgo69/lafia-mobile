<?php

declare(strict_types=1);

namespace App\Modules\Pharmacy\Models;

use App\Modules\Pharmacy\Enums\PharmacyZone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Pharmacy extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'zone',
        'city',
        'neighborhood',
        'address',
        'phone',
        'phone_alt',
        'latitude',
        'longitude',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'zone' => PharmacyZone::class,
            'latitude' => 'float',
            'longitude' => 'float',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return HasMany<PharmacyGuard, $this>
     */
    public function guards(): HasMany
    {
        return $this->hasMany(PharmacyGuard::class);
    }

    public function isOnGuardToday(): bool
    {
        $today = now()->toDateString();

        return $this->guards()
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->exists();
    }

    public function distanceFrom(float $lat, float $lng): float
    {
        if ($this->latitude === null || $this->longitude === null) {
            return PHP_FLOAT_MAX;
        }

        $earthRadius = 6371;
        $dLat = deg2rad($lat - $this->latitude);
        $dLng = deg2rad($lng - $this->longitude);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($this->latitude)) * cos(deg2rad($lat)) * sin($dLng / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
