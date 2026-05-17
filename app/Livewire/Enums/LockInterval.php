<?php

declare(strict_types=1);

namespace App\Livewire\Enums;

enum LockInterval: string
{
    case ALWAYS      = '0';
    case ONE_MIN     = '1';
    case FIVE_MIN    = '5';
    case FIFTEEN_MIN = '15';

    public function label(): string
    {
        return match ($this) {
            self::ALWAYS      => __('vault.lock_every_time'),
            self::ONE_MIN     => __('vault.lock_1min'),
            self::FIVE_MIN    => __('vault.lock_5min'),
            self::FIFTEEN_MIN => __('vault.lock_15min'),
        };
    }

    public function minutes(): int
    {
        return (int) $this->value;
    }
}
