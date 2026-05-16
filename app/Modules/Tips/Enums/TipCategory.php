<?php

declare(strict_types=1);

namespace App\Modules\Tips\Enums;

enum TipCategory: string
{
    case TELECOM = 'telecom';
    case ELECTRICITE = 'electricite';
    case EAU = 'eau';
    case SANTE = 'sante';
    case SECURITE = 'securite';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::TELECOM => 'Telecom',
            self::ELECTRICITE => 'Electricite',
            self::EAU => 'Eau',
            self::SANTE => 'Sante',
            self::SECURITE => 'Securite',
            self::ADMIN => 'Administratif',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::TELECOM => 'phone',
            self::ELECTRICITE => 'bolt',
            self::EAU => 'beaker',
            self::SANTE => 'heart-pulse',
            self::SECURITE => 'shield-check',
            self::ADMIN => 'document-text',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::TELECOM => 'blue',
            self::ELECTRICITE => 'yellow',
            self::EAU => 'cyan',
            self::SANTE => 'green',
            self::SECURITE => 'red',
            self::ADMIN => 'purple',
        };
    }
}
