<?php

declare(strict_types=1);

namespace App\Modules\Emergency\Enums;

enum EmergencyCategory: string
{
    case FIRE = 'fire';
    case MEDICAL = 'medical';
    case POLICE = 'police';
    case CHILD_PROTECTION = 'child';
    case CIVIL_PROTECTION = 'civil_protection';
    case GENERAL = 'general';

    public function label(): string
    {
        return __('emergency.cat_' . $this->value);
    }

    public function shortCode(): string
    {
        return match ($this) {
            self::FIRE => '118',
            self::MEDICAL => '136',
            self::POLICE => '117',
            self::CHILD_PROTECTION => '111',
            self::CIVIL_PROTECTION => '160',
            self::GENERAL => '118',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::FIRE => 'flame',
            self::MEDICAL => 'heart-pulse',
            self::POLICE => 'shield-alert',
            self::CHILD_PROTECTION => 'baby',
            self::CIVIL_PROTECTION => 'siren',
            self::GENERAL => 'phone-call',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::FIRE => 'danger',
            self::MEDICAL => 'success',
            self::POLICE => 'primary',
            self::CHILD_PROTECTION => 'warning',
            self::CIVIL_PROTECTION => 'primary',
            self::GENERAL => 'gray',
        };
    }

    public function iconColorClass(): string
    {
        return match ($this) {
            self::FIRE => 'text-danger bg-danger-light dark:bg-danger/20',
            self::MEDICAL => 'text-success bg-success-light dark:bg-success/20',
            self::POLICE => 'text-primary bg-primary-light dark:bg-primary/20',
            self::CHILD_PROTECTION => 'text-warning bg-warning-light dark:bg-warning/20',
            self::CIVIL_PROTECTION => 'text-primary bg-primary-light dark:bg-primary/20',
            self::GENERAL => 'text-gray-600 bg-gray-100',
        };
    }
}
