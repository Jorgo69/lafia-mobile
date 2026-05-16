<?php

declare(strict_types=1);

namespace App\Modules\Sync\Models;

use Illuminate\Database\Eloquent\Model;

final class SyncSnapshot extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'resource';
    protected $keyType = 'string';

    protected $fillable = [
        'resource',
        'version',
        'data',
        'item_count',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'item_count' => 'integer',
            'created_at' => 'datetime',
        ];
    }
}
