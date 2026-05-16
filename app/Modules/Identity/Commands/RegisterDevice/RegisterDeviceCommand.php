<?php

declare(strict_types=1);

namespace App\Modules\Identity\Commands\RegisterDevice;

use App\Shared\Bus\Command;

final readonly class RegisterDeviceCommand implements Command
{
    public function __construct(
        public int $userId,
        public string $deviceUuid,
        public ?string $deviceName = null,
        public ?string $devicePlatform = null,
        public int $guardianThreshold = 2,
    ) {}
}
