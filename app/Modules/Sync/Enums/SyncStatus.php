<?php

declare(strict_types=1);

namespace App\Modules\Sync\Enums;

enum SyncStatus: string
{
    case UP_TO_DATE = 'up_to_date';
    case UPDATED = 'updated';
    case FAILED = 'failed';
    case OFFLINE = 'offline';
}
