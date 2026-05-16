<?php

declare(strict_types=1);

namespace App\Modules\Identity\Models;

use App\Models\User;
use App\Modules\Identity\Enums\DeviceStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Identity extends Model
{
    use HasUlids;

    protected $fillable = [
        'user_id',
        'device_uuid',
        'device_name',
        'device_platform',
        'status',
        'public_key_fingerprint',
        'guardian_threshold',
    ];

    /** @return array<string, mixed> */
    protected function casts(): array
    {
        return [
            'status' => DeviceStatus::class,
            'guardian_threshold' => 'integer',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<Guardian, $this> */
    public function guardians(): HasMany
    {
        return $this->hasMany(Guardian::class);
    }

    /** @return HasMany<RecoveryRequest, $this> */
    public function recoveryRequests(): HasMany
    {
        return $this->hasMany(RecoveryRequest::class);
    }
}
