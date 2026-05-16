@props([
    'label',
    'items',
    'wireModel',
    'wireAdd',
    'wireRemove',
    'placeholder' => 'Ajouter...',
    'variant' => 'default',
    'icon' => null,
])

@php
    $badgeVariants = [
        'default' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
        'warning' => 'bg-warning-100 text-warning-600 dark:bg-warning/20 dark:text-warning-100',
        'primary' => 'bg-primary-100 text-primary-700 dark:bg-primary/20 dark:text-primary-100',
        'success' => 'bg-success-100 text-success-700 dark:bg-success/20 dark:text-success-100',
    ];
    $badgeClass = $badgeVariants[$variant] ?? $badgeVariants['default'];
@endphp

<div class="space-y-2">
    <div class="flex items-center justify-between">
        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
            @if ($icon)
                <x-icon :name="$icon" class="w-4 h-4" />
            @endif
            {{ $label }} ({{ count($items) }})
        </label>
    </div>

    {{-- Input + Add Button --}}
    <div class="flex gap-2">
        <input
            type="text"
            wire:model="{{ $wireModel }}"
            wire:keydown.enter="{{ $wireAdd }}"
            placeholder="{{ $placeholder }}"
            class="flex-1 rounded-xl border-gray-300 dark:border-gray-600 dark:bg-surface-dark-card dark:text-gray-200 text-sm"
        >
        <button
            type="button"
            wire:click="{{ $wireAdd }}"
            class="px-3 py-2 rounded-xl bg-success text-white text-sm font-semibold shrink-0 active:scale-95 transition-transform"
        >
            <x-icon name="plus" class="w-4 h-4" />
        </button>
    </div>

    {{-- Items List --}}
    @if (count($items) > 0)
        <div class="flex flex-wrap gap-1.5">
            @foreach ($items as $index => $item)
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $badgeClass }}">
                    {{ $item }}
                    <button
                        type="button"
                        wire:click="{{ $wireRemove }}({{ $index }})"
                        class="ml-0.5 hover:text-danger transition-colors"
                    >
                        <x-icon name="x" class="w-3 h-3" />
                    </button>
                </span>
            @endforeach
        </div>
    @endif
</div>
