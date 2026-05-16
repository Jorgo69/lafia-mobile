<?php

declare(strict_types=1);

namespace App\Modules\Identity\Commands\ApproveRecovery;

use App\Shared\Bus\Command;

final readonly class ApproveRecoveryCommand implements Command
{
    public function __construct(
        public string $recoveryRequestId,
        public string $guardianId,
        public string $reEncryptedFragment,
    ) {}
}
