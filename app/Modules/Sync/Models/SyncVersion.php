<?php

declare(strict_types=1);

namespace App\Modules\Sync\Models;

use Illuminate\Database\Eloquent\Model;

final class SyncVersion extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'resource';
    protected $keyType = 'string';

    protected $fillable = [
        'resource',
        'version',
        'etag',
        'last_synced_at',
        'next_sync_after',
    ];

    protected function casts(): array
    {
        return [
            'last_synced_at' => 'datetime',
            'next_sync_after' => 'datetime',
        ];
    }

    public function isDue(): bool
    {
        return $this->next_sync_after === null || $this->next_sync_after->isPast();
    }
}
