<?php

declare(strict_types=1);

namespace App\Modules\Sync\Handlers;

use App\Modules\Sync\Services\SyncHandlerInterface;
use App\Modules\Tips\Models\PracticalTip;
use Illuminate\Support\Facades\DB;

final class PracticalTipsSyncHandler implements SyncHandlerInterface
{
    /**
     * @param array<int, array{slug: string, category: string, title: string, content: string, source?: string, valid_from?: string, valid_until?: string, is_pinned?: bool, sort_order?: int}> $data
     */
    public function apply(array $data): int
    {
        return DB::transaction(function () use ($data): int {
            $updated = 0;

            foreach ($data as $tip) {
                PracticalTip::updateOrCreate(
                    ['slug' => $tip['slug']],
                    [
                        'category' => $tip['category'],
                        'title' => $tip['title'],
                        'content' => $tip['content'],
                        'source' => $tip['source'] ?? null,
                        'valid_from' => $tip['valid_from'] ?? null,
                        'valid_until' => $tip['valid_until'] ?? null,
                        'is_pinned' => $tip['is_pinned'] ?? false,
                        'is_active' => $tip['is_active'] ?? true,
                        'sort_order' => $tip['sort_order'] ?? 0,
                    ],
                );
                $updated++;
            }

            return $updated;
        });
    }

    public function snapshot(): array
    {
        return PracticalTip::where('is_active', true)
            ->get()
            ->map(fn (PracticalTip $tip) => [
                'slug' => $tip->slug,
                'category' => $tip->category->value ?? $tip->category,
                'title' => $tip->title,
                'content' => $tip->content,
                'source' => $tip->source,
                'valid_from' => $tip->valid_from,
                'valid_until' => $tip->valid_until,
                'is_pinned' => $tip->is_pinned,
                'is_active' => $tip->is_active,
                'sort_order' => $tip->sort_order,
            ])
            ->toArray();
    }

    public function restore(array $data): int
    {
        return DB::transaction(function () use ($data): int {
            $restored = 0;

            PracticalTip::query()->delete();

            foreach ($data as $tip) {
                PracticalTip::create([
                    'slug' => $tip['slug'],
                    'category' => $tip['category'],
                    'title' => $tip['title'],
                    'content' => $tip['content'],
                    'source' => $tip['source'] ?? null,
                    'valid_from' => $tip['valid_from'] ?? null,
                    'valid_until' => $tip['valid_until'] ?? null,
                    'is_pinned' => $tip['is_pinned'] ?? false,
                    'is_active' => $tip['is_active'] ?? true,
                    'sort_order' => $tip['sort_order'] ?? 0,
                ]);
                $restored++;
            }

            return $restored;
        });
    }
}
