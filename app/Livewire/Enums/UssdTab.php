<?php

declare(strict_types=1);

namespace App\Livewire\Enums;

enum UssdTab: string
{
    case LIST      = 'list';
    case FAVORITES = 'favorites';

    public function label(): string
    {
        return match ($this) {
            self::LIST      => __('ussd.list_mode'),
            self::FAVORITES => __('ussd.favorites'),
        };
    }
}
