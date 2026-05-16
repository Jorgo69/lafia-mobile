<?php

declare(strict_types=1);

namespace App\Modules\Ussd\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class UssdFavorite extends Model
{
    protected $fillable = [
        'device_id',
        'ussd_code_id',
        'saved_params',
        'custom_label',
        'use_count',
        'last_used_at',
    ];

    protected function casts(): array
    {
        return [
            'saved_params' => 'array',
            'last_used_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<UssdCode, $this>
     */
    public function ussdCode(): BelongsTo
    {
        return $this->belongsTo(UssdCode::class);
    }

    public function incrementUse(): void
    {
        $this->increment('use_count');
        $this->update(['last_used_at' => now()]);
    }
}
