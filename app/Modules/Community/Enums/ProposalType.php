<?php

declare(strict_types=1);

namespace App\Modules\Community\Enums;

enum ProposalType: string
{
    case PHARMACY = 'pharmacy';
    case EMERGENCY_CONTACT = 'emergency_contact';
    case USSD_CODE = 'ussd_code';

    public function label(): string
    {
        return match ($this) {
            self::PHARMACY => __('common.pharmacy'),
            self::EMERGENCY_CONTACT => __('common.emergency_contact'),
            self::USSD_CODE => __('common.ussd_code'),
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PHARMACY => 'building-storefront',
            self::EMERGENCY_CONTACT => 'phone',
            self::USSD_CODE => 'hashtag',
        };
    }

    public function datasetSlug(): string
    {
        return match ($this) {
            self::PHARMACY => 'pharmacies',
            self::EMERGENCY_CONTACT => 'emergency_contacts',
            self::USSD_CODE => 'short_codes',
        };
    }

    /**
     * @return array<string, string>
     */
    public function requiredFields(): array
    {
        return match ($this) {
            self::PHARMACY => ['name', 'phone', 'zone'],
            self::EMERGENCY_CONTACT => ['name', 'phone', 'commune'],
            self::USSD_CODE => ['code', 'operator', 'description'],
        };
    }
}
