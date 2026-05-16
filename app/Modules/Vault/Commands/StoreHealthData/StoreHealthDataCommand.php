<?php

declare(strict_types=1);

namespace App\Modules\Vault\Commands\StoreHealthData;

use App\Modules\Vault\DTOs\HealthData;
use App\Shared\Bus\Command;

final readonly class StoreHealthDataCommand implements Command
{
    public function __construct(
        public int $userId,
        public string $label,
        public HealthData $healthData,
    ) {}
}
