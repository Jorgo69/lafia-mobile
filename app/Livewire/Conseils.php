<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Modules\Tips\Enums\TipCategory;
use App\Modules\Tips\Models\PracticalTip;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
final class Conseils extends Component
{
    public string $category = '';

    public function setCategory(string $category): void
    {
        if ($category !== '' && TipCategory::tryFrom($category) === null) {
            return;
        }
        $this->category = $category;
    }

    public function render(): View
    {
        /** @var Collection<int, PracticalTip> $tips */
        $tips = PracticalTip::active()
            ->when($this->category !== '', fn ($q) => $q->where('category', $this->category))
            ->orderByDesc('is_pinned')
            ->orderBy('sort_order')
            ->get();

        return view('livewire.conseils', [
            'tips' => $tips,
            'categories' => TipCategory::cases(),
        ]);
    }
}
