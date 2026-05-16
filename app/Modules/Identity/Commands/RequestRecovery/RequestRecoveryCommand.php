<?php

declare(strict_types=1);

namespace App\Modules\Identity\Commands\RequestRecovery;

use App\Shared\Bus\Command;

final readonly class RequestRecoveryCommand implements Command
{
    public function __construct(
        public string $identityId,
        public string $newDeviceUuid,
        public string $newDevicePublicKey,
    ) {}
}
