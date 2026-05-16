<?php

declare(strict_types=1);

namespace App\Modules\Emergency\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Department extends Model
{
    use HasUlids;

    protected $fillable = [
        'name',
        'code',
    ];

    /** @return HasMany<EmergencyCenter, $this> */
    public function emergencyCenters(): HasMany
    {
        return $this->hasMany(EmergencyCenter::class);
    }
}
