<?php

declare(strict_types=1);

namespace App\Modules\Emergency\Resources;

use App\Modules\Emergency\Models\EmergencyContact;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin EmergencyContact */
final class EmergencyContactResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'operator' => $this->operator->value,
            'operator_label' => $this->operator->label(),
            'phone_number' => $this->phone_number,
            'priority_score' => $this->priority_score,
            'provider_routing' => $this->provider_routing,
            'is_active' => $this->is_active,
        ];
    }
}
