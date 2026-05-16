<?php

declare(strict_types=1);

namespace App\Modules\Identity\DTOs;

final readonly class DeviceRegistrationData
{
    public function __construct(
        public string $deviceUuid,
        public ?string $deviceName = null,
        public ?string $devicePlatform = null,
        public int $guardianThreshold = 2,
    ) {}
}
