<?php

declare(strict_types=1);

namespace App\Modules\Identity\Queries\GetIdentity;

use App\Shared\Bus\Query;

final readonly class GetIdentityQuery implements Query
{
    public function __construct(
        public int $userId,
    ) {}
}
