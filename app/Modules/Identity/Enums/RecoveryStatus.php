<?php

declare(strict_types=1);

namespace App\Modules\Identity\Enums;

enum RecoveryStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'En attente des gardiens',
            self::IN_PROGRESS => 'Fragments en cours de collecte',
            self::COMPLETED => 'Cle restauree',
            self::EXPIRED => 'Expire',
            self::CANCELLED => 'Annule',
        };
    }
}
