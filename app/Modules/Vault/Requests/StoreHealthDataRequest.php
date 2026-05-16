<?php

declare(strict_types=1);

namespace App\Modules\Vault\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreHealthDataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:255'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'allergies' => ['nullable', 'array'],
            'allergies.*' => ['string', 'max:255'],
            'medications' => ['nullable', 'array'],
            'medications.*' => ['string', 'max:255'],
            'conditions' => ['nullable', 'array'],
            'conditions.*' => ['string', 'max:255'],
            'emergency_notes' => ['nullable', 'string', 'max:2000'],
            'weight_kg' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'height_cm' => ['nullable', 'numeric', 'min:0', 'max:300'],
        ];
    }
}
