<?php

declare(strict_types=1);

namespace App\Modules\Identity\Enums;

enum GuardianStatus: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REVOKED = 'revoked';

    public function label(): string
    {
        return __('circle.guardian_status_' . $this->value);
    }

    public function icon(): string
    {
        return match ($this) {
            self::PENDING => 'clock',
            self::ACCEPTED => 'user-check',
            self::REVOKED => 'x-circle',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-400',
            self::ACCEPTED => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-400',
            self::REVOKED => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
        };
    }
}
