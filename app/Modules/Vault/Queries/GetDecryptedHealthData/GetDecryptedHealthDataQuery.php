<?php

declare(strict_types=1);

namespace App\Modules\Vault\Queries\GetDecryptedHealthData;

use App\Shared\Bus\Query;

final readonly class GetDecryptedHealthDataQuery implements Query
{
    public function __construct(
        public string $vaultId,
        public int $userId,
    ) {}
}
