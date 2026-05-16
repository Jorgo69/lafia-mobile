<?php

declare(strict_types=1);

namespace App\Modules\Vault\Models;

use App\Models\User;
use App\Modules\Vault\Enums\VaultDataType;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Vault extends Model
{
    use HasUlids;

    protected $fillable = [
        'user_id',
        'data_type',
        'label',
        'encrypted_payload',
        'public_key_fingerprint',
    ];

    /** @return array<string, mixed> */
    protected function casts(): array
    {
        return [
            'data_type' => VaultDataType::class,
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
