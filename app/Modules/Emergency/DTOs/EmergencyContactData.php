<?php

declare(strict_types=1);

namespace App\Modules\Emergency\DTOs;

use App\Shared\Enums\Operator;

final readonly class EmergencyContactData
{
    public function __construct(
        public Operator $operator,
        public string $phoneNumber,
        public int $priorityScore = 0,
        public ?string $providerRouting = null,
    ) {}
}
