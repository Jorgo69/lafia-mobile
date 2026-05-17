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

    public function chipClass(): string
    {
        return match ($this) {
            self::MTN     => 'bg-brand-mtn text-gray-900',
            self::MOOV    => 'bg-brand-moov text-white',
            self::CELTIIS => 'bg-brand-celtiis text-white',
        };
    }
}
