<?php

declare(strict_types=1);

namespace App\Modules\Emergency\Models;

use App\Modules\Emergency\Enums\EmergencyCategory;
use App\Modules\Emergency\Enums\EmergencyCenterType;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class EmergencyCenter extends Model
{
    use HasUlids;

    protected $fillable = [
        'department_id',
        'name',
        'slug',
        'type',
        'category',
        'latitude',
        'longitude',
        'is_active',
    ];

    /** @return array<string, mixed> */
    protected function casts(): array
    {
        return [
            'type' => EmergencyCenterType::class,
            'category' => EmergencyCategory::class,
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_active' => 'boolean',
        ];
    }

    /** @return BelongsTo<Department, $this> */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /** @return HasMany<EmergencyContact, $this> */
    public function contacts(): HasMany
    {
        return $this->hasMany(EmergencyContact::class);
    }
}
