<?php

declare(strict_types=1);

namespace App\Modules\Community\Enums;

enum ServiceReportType: string
{
    case CLOSED         = 'closed';
    case OPEN           = 'open';
    case NOT_RESPONDING = 'not_responding';
    case WRONG_NUMBER   = 'wrong_number';

    public function label(): string
    {
        return match ($this) {
            self::CLOSED         => __('common.report_closed'),
            self::OPEN           => __('common.report_open'),
            self::NOT_RESPONDING => __('common.report_not_responding'),
            self::WRONG_NUMBER   => __('common.report_wrong_number'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::CLOSED         => 'bg-danger-100 dark:bg-danger-900/30 text-danger-500',
            self::OPEN           => 'bg-success-100 dark:bg-success-900/30 text-success-500',
            self::NOT_RESPONDING => 'bg-warning-100 dark:bg-warning-900/30 text-warning-500',
            self::WRONG_NUMBER   => 'bg-danger-50 dark:bg-danger-900/20 text-danger-400',
        };
    }
}
