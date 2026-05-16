<?php

declare(strict_types=1);

namespace App\Modules\Identity\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class RecoveryFragment extends Model
{
    use HasUlids;

    protected $fillable = [
        'recovery_request_id',
        'guardian_id',
        're_encrypted_fragment',
    ];

    /** @return BelongsTo<RecoveryRequest, $this> */
    public function recoveryRequest(): BelongsTo
    {
        return $this->belongsTo(RecoveryRequest::class);
    }

    /** @return BelongsTo<Guardian, $this> */
    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class);
    }
}
