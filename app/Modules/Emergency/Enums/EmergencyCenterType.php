<?php

declare(strict_types=1);

namespace App\Modules\Emergency\Enums;

enum EmergencyCenterType: string
{
    case CCPC = 'ccpc';
    case ADPC = 'adpc';
    case NATIONAL = 'national';

    public function label(): string
    {
        return __('emergency.center_type_' . $this->value);
    }

    public function icon(): string
    {
        return match ($this) {
            self::CCPC => 'map-pin',
            self::ADPC => 'building-storefront',
            self::NATIONAL => 'siren',
        };
    }
}
