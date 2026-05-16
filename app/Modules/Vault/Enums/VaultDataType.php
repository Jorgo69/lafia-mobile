<?php

declare(strict_types=1);

namespace App\Modules\Vault\Enums;

enum VaultDataType: string
{
    case HEALTH = 'health';
    case IDENTITY = 'identity';
    case INSURANCE = 'insurance';
    case ALLERGY = 'allergy';
    case BLOOD_TYPE = 'blood_type';
    case EMERGENCY_CONTACT = 'emergency_contact';
    case MEDICAL_RECORD = 'medical_record';

    public function label(): string
    {
        return match ($this) {
            self::HEALTH => 'Donnees de sante',
            self::IDENTITY => 'Piece d\'identite',
            self::INSURANCE => 'Assurance',
            self::ALLERGY => 'Allergies',
            self::BLOOD_TYPE => 'Groupe sanguin',
            self::EMERGENCY_CONTACT => 'Contact d\'urgence personnel',
            self::MEDICAL_RECORD => 'Dossier medical',
        };
    }

    public function isSensitive(): bool
    {
        return match ($this) {
            self::HEALTH, self::IDENTITY, self::MEDICAL_RECORD, self::INSURANCE => true,
            self::ALLERGY, self::BLOOD_TYPE, self::EMERGENCY_CONTACT => false,
        };
    }
}
