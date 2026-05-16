<?php

declare(strict_types=1);

namespace App\Modules\Emergency\Resources;

use App\Modules\Emergency\Models\EmergencyServiceUpdate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin EmergencyServiceUpdate */
final class EmergencyServiceUpdateResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->value,
            'reported_issue' => $this->reported_issue,
            'suggested_phone_number' => $this->suggested_phone_number,
            'details' => $this->details,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
