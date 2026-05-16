<?php

declare(strict_types=1);

namespace App\Modules\Emergency\DTOs;

use App\Modules\Emergency\Enums\EmergencyCategory;
use App\Shared\Enums\Operator;

final readonly class EmergencyServiceData
{
    /**
     * @param array<EmergencyContactData> $contacts
     */
    public function __construct(
        public string $centerName,
        public string $departmentName,
        public EmergencyCategory $category,
        public array $contacts,
        public int $priorityScore = 0,
        public ?string $providerRouting = null,
        public ?float $distanceKm = null,
    ) {}

    public function getContactForOperator(Operator $operator): ?EmergencyContactData
    {
        foreach ($this->contacts as $contact) {
            if ($contact->operator === $operator) {
                return $contact;
            }
        }

        return null;
    }

    public function getBestContact(Operator $userOperator): EmergencyContactData
    {
        $operatorContact = $this->getContactForOperator($userOperator);

        if ($operatorContact !== null) {
            return $operatorContact;
        }

        return $this->contacts[0];
    }
}
