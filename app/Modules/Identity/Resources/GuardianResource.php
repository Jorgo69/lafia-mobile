<?php

declare(strict_types=1);

namespace App\Modules\Identity\Resources;

use App\Modules\Identity\Models\Guardian;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Guardian */
final class GuardianResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'alias' => $this->guardian_alias,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'fragment_index' => $this->fragment_index,
            'accepted_at' => $this->accepted_at?->toIso8601String(),
        ];
    }
}
