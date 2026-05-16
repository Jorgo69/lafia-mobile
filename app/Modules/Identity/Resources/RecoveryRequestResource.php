<?php

declare(strict_types=1);

namespace App\Modules\Identity\Resources;

use App\Modules\Identity\Models\RecoveryRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin RecoveryRequest */
final class RecoveryRequestResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'fragments_needed' => $this->fragments_needed,
            'fragments_received' => $this->fragments_received,
            'new_device_uuid' => $this->new_device_uuid,
            'expires_at' => $this->expires_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
