<?php

declare(strict_types=1);

namespace App\Livewire\Enums;

enum PharmacyViewMode: string
{
    case GUARD   = 'guard';
    case ALL     = 'all';
    case NEAREST = 'nearest';

    public function label(): string
    {
        return match ($this) {
            self::GUARD   => __('pharma.on_guard'),
            self::ALL     => __('pharma.all'),
            self::NEAREST => __('pharma.nearest'),
        };
    }
}
