<?php

declare(strict_types=1);

namespace App\Modules\Emergency\Models;

use App\Modules\Emergency\Enums\UpdateStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class EmergencyServiceUpdate extends Model
{
    use HasUlids;

    protected $fillable = [
        'emergency_contact_id',
        'status',
        'reported_issue',
        'suggested_phone_number',
        'details',
        'reporter_latitude',
        'reporter_longitude',
    ];

    /** @return array<string, mixed> */
    protected function casts(): array
    {
        return [
            'status' => UpdateStatus::class,
            'reporter_latitude' => 'decimal:7',
            'reporter_longitude' => 'decimal:7',
        ];
    }

    /** @return BelongsTo<EmergencyContact, $this> */
    public function emergencyContact(): BelongsTo
    {
        return $this->belongsTo(EmergencyContact::class);
    }
}
