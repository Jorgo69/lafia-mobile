<?php

declare(strict_types=1);

namespace App\Modules\Emergency\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ReportServiceUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'emergency_contact_id' => ['required', 'string', 'exists:emergency_contacts,id'],
            'reported_issue' => ['required', 'string', 'max:500'],
            'suggested_phone_number' => ['nullable', 'string', 'max:20'],
            'details' => ['nullable', 'string', 'max:2000'],
            'reporter_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'reporter_longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }
}
