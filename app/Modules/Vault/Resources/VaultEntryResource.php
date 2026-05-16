<?php

declare(strict_types=1);

namespace App\Modules\Vault\Resources;

use App\Modules\Vault\Models\Vault;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Vault */
final class VaultEntryResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'data_type' => $this->data_type->value,
            'data_type_label' => $this->data_type->label(),
            'label' => $this->label,
            'public_key_fingerprint' => $this->public_key_fingerprint,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
