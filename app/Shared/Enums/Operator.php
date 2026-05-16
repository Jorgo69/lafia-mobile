<?php

declare(strict_types=1);

namespace App\Shared\Enums;

enum Operator: string
{
    case MTN = 'mtn';
    case MOOV = 'moov';
    case CELTIIS = 'celtiis';

    public function label(): string
    {
        return match ($this) {
            self::MTN => 'MTN Benin',
            self::MOOV => 'Moov Africa',
            self::CELTIIS => 'Celtiis',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::MTN => '#ffcc00',
            self::MOOV => '#0066ff',
            self::CELTIIS => '#e60000',
        };
    }
}
