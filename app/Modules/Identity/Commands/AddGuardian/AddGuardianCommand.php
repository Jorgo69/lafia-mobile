<?php

declare(strict_types=1);

namespace App\Modules\Identity\Commands\AddGuardian;

use App\Shared\Bus\Command;

final readonly class AddGuardianCommand implements Command
{
    public function __construct(
        public string $identityId,
        public int $userId,
        public string $guardianAlias,
        public string $guardianPublicKey,
    ) {}
}
