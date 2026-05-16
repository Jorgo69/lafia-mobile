<?php

declare(strict_types=1);

namespace App\Modules\Sync\Handlers;

use App\Modules\Sync\Services\SyncHandlerInterface;
use App\Modules\Ussd\Models\UssdCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class ShortCodesSyncHandler implements SyncHandlerInterface
{
    public function apply(array $data): int
    {
        $updated = 0;

        DB::transaction(function () use ($data, &$updated) {
            foreach ($data as $index => $entry) {
                $action = $entry['action'] ?? $entry['action_type'] ?? 'other';
                $slug = Str::slug($entry['operator'] . '-' . $entry['category'] . '-' . $action);

                // Map API action to app action_type (direct/guided/menu)
                $actionType = $this->resolveActionType($entry);

                $ussd = UssdCode::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'operator' => $entry['operator'],
                        'category' => $entry['category'] ?? 'other',
                        'action_type' => $actionType,
                        'code' => $entry['code'],
                        'label' => $entry['label'] ?? '',
                        'description' => $entry['description'] ?? null,
                        'sort_order' => $entry['sort_order'] ?? $index,
                        'is_active' => $entry['is_active'] ?? true,
                    ],
                );

                if ($ussd->wasRecentlyCreated || $ussd->wasChanged()) {
                    $updated++;
                }
            }
        });

        return $updated;
    }

    /**
     * @param array<string, mixed> $entry
     */
    private function resolveActionType(array $entry): string
    {
        // If the API already sends direct/guided/menu, use it
        if (isset($entry['action_type']) && in_array($entry['action_type'], ['direct', 'guided', 'menu'], true)) {
            return $entry['action_type'];
        }

        // Infer from category/action: bill payments and transfers need inputs → guided
        $action = $entry['action'] ?? '';
        $category = $entry['category'] ?? '';

        if (in_array($category, ['bills', 'mobile_money'], true) && in_array($action, ['transfer', 'sbee', 'soneb'], true)) {
            return 'guided';
        }

        // Everything else is direct (simple code to dial)
        return 'direct';
    }

    public function snapshot(): array
    {
        return UssdCode::where('is_active', true)
            ->get()
            ->map(fn (UssdCode $code) => [
                'operator' => $code->operator,
                'category' => $code->category,
                'action_type' => $code->action_type,
                'code' => $code->code,
                'label' => $code->label,
                'description' => $code->description,
                'sort_order' => $code->sort_order,
                'is_active' => $code->is_active,
            ])
            ->toArray();
    }

    public function restore(array $data): int
    {
        $restored = 0;

        DB::transaction(function () use ($data, &$restored) {
            UssdCode::query()->delete();

            foreach ($data as $index => $entry) {
                $action = $entry['action'] ?? $entry['action_type'] ?? 'other';
                $slug = Str::slug($entry['operator'] . '-' . $entry['category'] . '-' . $action);

                UssdCode::create([
                    'slug' => $slug,
                    'operator' => $entry['operator'],
                    'category' => $entry['category'] ?? 'other',
                    'action_type' => $entry['action_type'] ?? 'direct',
                    'code' => $entry['code'],
                    'label' => $entry['label'] ?? '',
                    'description' => $entry['description'] ?? null,
                    'sort_order' => $entry['sort_order'] ?? $index,
                    'is_active' => $entry['is_active'] ?? true,
                ]);
                $restored++;
            }
        });

        return $restored;
    }
}
