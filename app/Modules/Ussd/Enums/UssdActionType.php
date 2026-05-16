<?php

declare(strict_types=1);

namespace App\Modules\Ussd\Enums;

enum UssdActionType: string
{
    case DIRECT = 'direct';       // Code simple, on lance directement
    case GUIDED = 'guided';       // Necessite des inputs (numero, montant...)
    case MENU = 'menu';           // Ouvre un menu USSD interactif

    public function label(): string
    {
        return match ($this) {
            self::DIRECT => 'Lancer directement',
            self::GUIDED => 'Avec guide',
            self::MENU => 'Menu interactif',
        };
    }
}
