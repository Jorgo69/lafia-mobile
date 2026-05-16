<?php

declare(strict_types=1);

namespace App\Modules\Emergency\Queries\GetNearestCenter;

use App\Modules\Emergency\Enums\EmergencyCategory;
use App\Shared\Bus\Query;

final readonly class GetNearestCenterQuery implements Query
{
    public function __construct(
        public float $latitude,
        public float $longitude,
        public ?EmergencyCategory $category = null,
        public int $limit = 3,
    ) {}
}
