<?php

declare(strict_types=1);

namespace App\Livewire\Enums;

enum CoffreTab: string
{
    case SANTE = 'sante';
    case GARDIENS = 'gardiens';

    public function label(): string
    {
        return match ($this) {
            self::SANTE    => __('nav.health'),
            self::GARDIENS => __('nav.guardians'),
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::SANTE    => 'heart-pulse',
            self::GARDIENS => 'users',
        };
    }

    public function activeColor(): string
    {
        return match ($this) {
            self::SANTE    => 'bg-primary/10 text-primary',
            self::GARDIENS => 'bg-secondary-500/10 text-secondary-500',
        };
    }
}
