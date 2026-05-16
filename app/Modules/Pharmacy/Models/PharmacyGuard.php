<?php

declare(strict_types=1);

namespace App\Modules\Pharmacy\Models;

use App\Modules\Pharmacy\Enums\PharmacyZone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PharmacyGuard extends Model
{
    protected $fillable = [
        'pharmacy_id',
        'start_date',
        'end_date',
        'zone',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'zone' => PharmacyZone::class,
        ];
    }

    /**
     * @return BelongsTo<Pharmacy, $this>
     */
    public function pharmacy(): BelongsTo
    {
        return $this->belongsTo(Pharmacy::class);
    }
}
