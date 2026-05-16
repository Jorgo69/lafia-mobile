<?php

declare(strict_types=1);

namespace App\Modules\Community\Enums;

enum ReportStatus: string
{
    case QUEUED = 'queued';
    case SENDING = 'sending';
    case SENT = 'sent';
    case FAILED = 'failed';

    public function label(): string
    {
        return __('common.report_status_' . $this->value);
    }

    public function icon(): string
    {
        return match ($this) {
            self::QUEUED => 'clock',
            self::SENDING => 'loader-2',
            self::SENT => 'check-circle',
            self::FAILED => 'x-circle',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::QUEUED => 'bg-gray-100 text-gray-600',
            self::SENDING => 'bg-blue-100 text-blue-600',
            self::SENT => 'bg-green-100 text-green-600',
            self::FAILED => 'bg-red-100 text-red-600',
        };
    }
}
