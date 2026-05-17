<?php

declare(strict_types=1);

namespace App\Modules\Identity\Enums;

enum DeviceStatus: string
{
    case ACTIVE = 'active';
    case LOST = 'lost';
    case REVOKED = 'revoked';

    public function label(): string
    {
        return __('circle.device_status_' . $this->value);
    }

    public function icon(): string
    {
        return match ($this) {
            self::ACTIVE => 'check-circle',
            self::LOST => 'alert-triangle',
            self::REVOKED => 'x-circle',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::ACTIVE   => 'bg-success-100 text-success-700 dark:bg-success-900 dark:text-success-400',
            self::LOST     => 'bg-danger-100 text-danger-700 dark:bg-danger-900 dark:text-danger-400',
            self::REVOKED  => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
        };
    }
}
