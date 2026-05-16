<?php

declare(strict_types=1);

namespace App\Modules\Emergency\Queries\GetAllCenters;

use App\Modules\Emergency\Enums\EmergencyCategory;
use App\Shared\Bus\Query;

final readonly class GetAllCentersQuery implements Query
{
    public function __construct(
        public ?string $departmentCode = null,
        public ?EmergencyCategory $category = null,
    ) {}
}
