<?php

declare(strict_types=1);

namespace App\Modules\Tips\Enums;

enum TipCategory: string
{
    case TELECOM = 'telecom';
    case ELECTRICITE = 'electricite';
    case EAU = 'eau';
    case SANTE = 'sante';
    case SECURITE = 'securite';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::TELECOM => 'Telecom',
            self::ELECTRICITE => 'Electricite',
            self::EAU => 'Eau',
            self::SANTE => 'Sante',
            self::SECURITE => 'Securite',
            self::ADMIN => 'Administratif',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::TELECOM => 'phone',
            self::ELECTRICITE => 'bolt',
            self::EAU => 'beaker',
            self::SANTE => 'heart-pulse',
            self::SECURITE => 'shield-check',
            self::ADMIN => 'document-text',
        };
    }

    /** Classes bg+text pour icône ou chip en mode actif (fond solide, texte blanc). */
    public function chipClass(): string
    {
        return match ($this) {
            self::TELECOM     => 'bg-primary text-white',
            self::ELECTRICITE => 'bg-yellow-500 text-white',  // couleur unique électricité
            self::EAU         => 'bg-cyan-500 text-white',    // couleur unique eau
            self::SANTE       => 'bg-success text-white',
            self::SECURITE    => 'bg-danger text-white',
            self::ADMIN       => 'bg-purple-500 text-white',  // couleur unique administratif
        };
    }

    /** Classes bg+text pour icône dans un conteneur léger (fond pastel). */
    public function colorClass(): string
    {
        return match ($this) {
            self::TELECOM     => 'bg-primary-50 dark:bg-primary-900/20 text-primary-600',
            self::ELECTRICITE => 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600',
            self::EAU         => 'bg-cyan-50 dark:bg-cyan-900/20 text-cyan-600',
            self::SANTE       => 'bg-success-50 dark:bg-success-900/20 text-success-600',
            self::SECURITE    => 'bg-danger-50 dark:bg-danger-900/20 text-danger-600',
            self::ADMIN       => 'bg-purple-50 dark:bg-purple-900/20 text-purple-600',
        };
    }

    /** Classes border+bg pour les cartes (fond pastel + bordure). */
    public function cardClass(): string
    {
        return match ($this) {
            self::TELECOM     => 'border-primary-200 bg-primary-50 dark:border-primary-800 dark:bg-primary-900/20',
            self::ELECTRICITE => 'border-yellow-200 bg-yellow-50 dark:border-yellow-800 dark:bg-yellow-900/20',
            self::EAU         => 'border-cyan-200 bg-cyan-50 dark:border-cyan-800 dark:bg-cyan-900/20',
            self::SANTE       => 'border-success-200 bg-success-50 dark:border-success-800 dark:bg-success-900/20',
            self::SECURITE    => 'border-danger-200 bg-danger-50 dark:border-danger-800 dark:bg-danger-900/20',
            self::ADMIN       => 'border-purple-200 bg-purple-50 dark:border-purple-800 dark:bg-purple-900/20',
        };
    }
}
