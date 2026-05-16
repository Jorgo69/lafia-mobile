<?php

declare(strict_types=1);

namespace App\Modules\Identity\Resources;

use App\Modules\Identity\Models\Identity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Identity */
final class IdentityResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'device_uuid' => $this->device_uuid,
            'device_name' => $this->device_name,
            'device_platform' => $this->device_platform,
            'status' => $this->status->value,
            'public_key_fingerprint' => $this->public_key_fingerprint,
            'guardian_threshold' => $this->guardian_threshold,
            'guardians' => GuardianResource::collection($this->whenLoaded('guardians')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
