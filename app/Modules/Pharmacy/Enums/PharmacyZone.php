<?php

declare(strict_types=1);

namespace App\Modules\Pharmacy\Enums;

enum PharmacyZone: string
{
    case LITTORAL = 'littoral';
    case ATLANTIQUE = 'atlantique';
    case OUEME = 'oueme';
    case BORGOU = 'borgou';
    case ZOU = 'zou';
    case COLLINES = 'collines';
    case DONGA = 'donga';
    case ALIBORI = 'alibori';
    case ATACORA = 'atacora';
    case MONO = 'mono';
    case COUFFO = 'couffo';
    case PLATEAU = 'plateau';

    public function label(): string
    {
        return match ($this) {
            self::LITTORAL => 'Cotonou',
            self::ATLANTIQUE => 'Abomey-Calavi / Ouidah',
            self::OUEME => 'Porto-Novo / Seme-Krake',
            self::BORGOU => 'Parakou',
            self::ZOU => 'Abomey / Bohicon',
            self::COLLINES => 'Dassa / Savalou',
            self::DONGA => 'Djougou',
            self::ALIBORI => 'Kandi / Malanville',
            self::ATACORA => 'Natitingou',
            self::MONO => 'Lokossa / Come',
            self::COUFFO => 'Dogbo / Aplahoue',
            self::PLATEAU => 'Pobe / Ketou',
        };
    }
}
