<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Pharmacy;

use App\Modules\Pharmacy\Models\Pharmacy;
use Tests\TestCase;

final class PharmacyModelTest extends TestCase
{
    public function test_distance_from_haversine(): void
    {
        $pharmacy = new Pharmacy([
            'latitude' => 6.3670,
            'longitude' => 2.4020,
        ]);

        // Same point
        $this->assertEqualsWithDelta(0.0, $pharmacy->distanceFrom(6.3670, 2.4020), 0.01);

        // ~1km away
        $distance = $pharmacy->distanceFrom(6.3760, 2.4020);
        $this->assertGreaterThan(0.5, $distance);
        $this->assertLessThan(2.0, $distance);
    }

    public function test_distance_from_null_coordinates(): void
    {
        $pharmacy = new Pharmacy([
            'latitude' => null,
            'longitude' => null,
        ]);

        $this->assertSame(PHP_FLOAT_MAX, $pharmacy->distanceFrom(6.3670, 2.4020));
    }
}
