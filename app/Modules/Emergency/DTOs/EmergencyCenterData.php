<?php

declare(strict_types=1);

namespace App\Modules\Emergency\DTOs;

use App\Modules\Emergency\Enums\EmergencyCategory;
use App\Modules\Emergency\Enums\EmergencyCenterType;

final readonly class EmergencyCenterData
{
    public function __construct(
        public string $name,
        public string $slug,
        public string $departmentCode,
        public EmergencyCenterType $type,
        public EmergencyCategory $category,
        public ?float $latitude = null,
        public ?float $longitude = null,
    ) {}
}
