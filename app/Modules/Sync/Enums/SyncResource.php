<?php

declare(strict_types=1);

namespace App\Modules\Sync\Enums;

enum SyncResource: string
{
    case EMERGENCY_CONTACTS = 'emergency_contacts';
    case EMERGENCY_CENTERS = 'emergency_centers';
    case GPS_COORDINATES = 'gps_coordinates';
    case HEALTH_SCHEMA = 'health_schema';
    case SHORT_CODES = 'short_codes';
    case PRACTICAL_TIPS = 'practical_tips';
    case PHARMACIES = 'pharmacies';

    public function priority(): int
    {
        return match ($this) {
            self::SHORT_CODES => 100,
            self::EMERGENCY_CONTACTS => 90,
            self::EMERGENCY_CENTERS => 70,
            self::GPS_COORDINATES => 50,
            self::PRACTICAL_TIPS => 40,
            self::PHARMACIES => 35,
            self::HEALTH_SCHEMA => 30,
        };
    }

    public function syncIntervalMinutes(): int
    {
        return match ($this) {
            self::SHORT_CODES, self::EMERGENCY_CONTACTS => 60,
            self::EMERGENCY_CENTERS => 360,
            self::GPS_COORDINATES => 1440,
            self::PRACTICAL_TIPS => 720,
            self::PHARMACIES => 720,
            self::HEALTH_SCHEMA => 1440,
        };
    }
}
