<?php

declare(strict_types=1);

namespace App\Modules\Emergency\Enums;

enum UpdateStatus: string
{
    case PENDING = 'pending';
    case VERIFIED = 'verified';
    case REJECTED = 'rejected';
}
