<?php

declare(strict_types=1);

namespace App\Modules\Emergency\Resources;

use App\Modules\Emergency\Models\EmergencyCenter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin EmergencyCenter */
final class EmergencyCenterResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => $this->type->value,
            'type_label' => $this->type->label(),
            'category' => $this->category->value,
            'category_label' => $this->category->label(),
            'department' => $this->whenLoaded('department', fn () => [
                'name' => $this->department->name,
                'code' => $this->department->code,
            ]),
            'contacts' => EmergencyContactResource::collection($this->whenLoaded('contacts')),
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'distance_km' => $this->whenHas('distance_km', fn () => round((float) $this->getAttribute('distance_km'), 2)),
            'is_active' => $this->is_active,
        ];
    }
}
