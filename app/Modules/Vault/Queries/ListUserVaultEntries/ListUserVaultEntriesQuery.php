<?php

declare(strict_types=1);

namespace App\Modules\Vault\Queries\ListUserVaultEntries;

use App\Modules\Vault\Enums\VaultDataType;
use App\Shared\Bus\Query;

final readonly class ListUserVaultEntriesQuery implements Query
{
    public function __construct(
        public int $userId,
        public ?VaultDataType $dataType = null,
    ) {}
}
