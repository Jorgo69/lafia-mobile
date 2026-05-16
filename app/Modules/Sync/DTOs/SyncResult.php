<?php

declare(strict_types=1);

namespace App\Modules\Sync\DTOs;

use App\Modules\Sync\Enums\SyncResource;
use App\Modules\Sync\Enums\SyncStatus;

final readonly class SyncResult
{
    public function __construct(
        public SyncResource $resource,
        public SyncStatus $status,
        public ?string $newVersion = null,
        public int $itemsUpdated = 0,
        public ?string $error = null,
        public bool $canUndo = false,
        public ?string $previousVersion = null,
    ) {}
}
