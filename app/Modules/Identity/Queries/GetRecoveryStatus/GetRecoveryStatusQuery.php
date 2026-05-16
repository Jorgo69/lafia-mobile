<?php

declare(strict_types=1);

namespace App\Modules\Identity\Queries\GetRecoveryStatus;

use App\Shared\Bus\Query;

final readonly class GetRecoveryStatusQuery implements Query
{
    public function __construct(
        public string $recoveryRequestId,
    ) {}
}
