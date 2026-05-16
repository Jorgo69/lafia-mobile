<?php

declare(strict_types=1);

namespace App\Modules\Ussd\Enums;

enum UssdCategory: string
{
    case MOBILE_MONEY = 'mobile_money';
    case FORFAIT = 'forfait';
    case FACTURE = 'facture';
    case COMPTE = 'compte';
    case AUTRE = 'autre';

    public function label(): string
    {
        return match ($this) {
            self::MOBILE_MONEY => 'Mobile Money',
            self::FORFAIT => 'Forfaits',
            self::FACTURE => 'Factures',
            self::COMPTE => 'Mon Compte',
            self::AUTRE => 'Autres',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::MOBILE_MONEY => 'banknotes',
            self::FORFAIT => 'wifi',
            self::FACTURE => 'receipt',
            self::COMPTE => 'user-circle',
            self::AUTRE => 'ellipsis-horizontal',
        };
    }

    public function sortOrder(): int
    {
        return match ($this) {
            self::MOBILE_MONEY => 1,
            self::FORFAIT => 2,
            self::FACTURE => 3,
            self::COMPTE => 4,
            self::AUTRE => 5,
        };
    }
}
