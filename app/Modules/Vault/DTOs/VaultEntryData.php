<?php

declare(strict_types=1);

namespace App\Modules\Vault\DTOs;

use App\Modules\Vault\Enums\VaultDataType;

final readonly class VaultEntryData
{
    public function __construct(
        public string $id,
        public VaultDataType $dataType,
        public string $label,
        public string $createdAt,
        public ?string $updatedAt = null,
    ) {}
}
