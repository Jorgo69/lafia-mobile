<?php

declare(strict_types=1);

namespace App\Modules\Identity\Models;

use App\Modules\Identity\Enums\GuardianStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Guardian extends Model
{
    use HasUlids;

    protected $fillable = [
        'identity_id',
        'guardian_alias',
        'guardian_public_key',
        'encrypted_fragment',
        'fragment_index',
        'status',
        'accepted_at',
    ];

    /** @return array<string, mixed> */
    protected function casts(): array
    {
        return [
            'status' => GuardianStatus::class,
            'fragment_index' => 'integer',
            'accepted_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Identity, $this> */
    public function identity(): BelongsTo
    {
        return $this->belongsTo(Identity::class);
    }
}
