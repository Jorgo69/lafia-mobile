<?php

declare(strict_types=1);

namespace App\Modules\Emergency\Models;

use App\Shared\Enums\Operator;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class EmergencyContact extends Model
{
    use HasUlids;

    protected $fillable = [
        'emergency_center_id',
        'operator',
        'phone_number',
        'priority_score',
        'provider_routing',
        'is_active',
    ];

    /** @return array<string, mixed> */
    protected function casts(): array
    {
        return [
            'operator' => Operator::class,
            'priority_score' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /** @return BelongsTo<EmergencyCenter, $this> */
    public function emergencyCenter(): BelongsTo
    {
        return $this->belongsTo(EmergencyCenter::class);
    }

    /** @return HasMany<EmergencyServiceUpdate, $this> */
    public function serviceUpdates(): HasMany
    {
        return $this->hasMany(EmergencyServiceUpdate::class);
    }
}
