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
            self::PENDING  => 'bg-warning-100 text-warning-700 dark:bg-warning-900 dark:text-warning-400',
            self::ACCEPTED => 'bg-success-100 text-success-700 dark:bg-success-900 dark:text-success-400',
            self::REVOKED  => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
        };
    }
}
