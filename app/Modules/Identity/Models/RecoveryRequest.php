<?php

declare(strict_types=1);

namespace App\Modules\Identity\Models;

use App\Modules\Identity\Enums\RecoveryStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class RecoveryRequest extends Model
{
    use HasUlids;

    protected $fillable = [
        'identity_id',
        'new_device_uuid',
        'new_device_public_key',
        'status',
        'fragments_needed',
        'fragments_received',
        'expires_at',
        'completed_at',
    ];

    /** @return array<string, mixed> */
    protected function casts(): array
    {
        return [
            'status' => RecoveryStatus::class,
            'fragments_needed' => 'integer',
            'fragments_received' => 'integer',
            'expires_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Identity, $this> */
    public function identity(): BelongsTo
    {
        return $this->belongsTo(Identity::class);
    }

    /** @return HasMany<RecoveryFragment, $this> */
    public function fragments(): HasMany
    {
        return $this->hasMany(RecoveryFragment::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function hasEnoughFragments(): bool
    {
        return $this->fragments_received >= $this->fragments_needed;
    }
}
