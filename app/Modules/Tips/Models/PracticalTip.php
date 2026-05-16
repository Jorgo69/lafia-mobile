<?php

declare(strict_types=1);

namespace App\Modules\Tips\Models;

use App\Modules\Tips\Enums\TipCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final class PracticalTip extends Model
{
    protected $fillable = [
        'slug',
        'category',
        'title',
        'content',
        'source',
        'valid_from',
        'valid_until',
        'is_pinned',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'category' => TipCategory::class,
            'valid_from' => 'date',
            'valid_until' => 'date',
            'is_pinned' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @param Builder<PracticalTip> $query
     */
    public function scopeActive(Builder $query): void
    {
        $today = now()->toDateString();

        $query->where('is_active', true)
            ->where(fn (Builder $q) => $q
                ->whereNull('valid_from')
                ->orWhere('valid_from', '<=', $today))
            ->where(fn (Builder $q) => $q
                ->whereNull('valid_until')
                ->orWhere('valid_until', '>=', $today));
    }

    /**
     * @param Builder<PracticalTip> $query
     */
    public function scopePinned(Builder $query): void
    {
        $query->where('is_pinned', true);
    }

    public function isExpired(): bool
    {
        return $this->valid_until !== null && $this->valid_until->isPast();
    }
}
