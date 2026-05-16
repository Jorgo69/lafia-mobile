<?php

declare(strict_types=1);

namespace App\Modules\Identity\DTOs;

final readonly class GuardianData
{
    public function __construct(
        public string $alias,
        public string $publicKey,
    ) {}
}
