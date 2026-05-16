@props([
    'title' => '',
    'back' => false,
])

<header class="sticky top-0 z-40 bg-white/80 dark:bg-gray-900/80 backdrop-blur-lg border-b border-gray-200/50 dark:border-gray-800/50 safe-top">
    <div class="flex items-center justify-between h-12 px-4">
        <div class="flex items-center gap-3 min-w-0">
            @if($back)
                <button @click="window.history.back()" class="p-2 -ml-2 press-feedback rounded-xl">
                    <x-icon name="chevron-left" class="w-5 h-5 text-gray-600 dark:text-gray-400" />
                </button>
            @endif
            @if($title)
                <h1 class="text-lg font-bold truncate">{{ $title }}</h1>
            @endif
        </div>
        <div class="flex items-center gap-1">
            {{ $slot }}
            <a href="/settings" wire:navigate class="p-2 press-feedback rounded-lg">
                <x-icon name="cog-6-tooth" class="w-5 h-5 text-gray-400" />
            </a>
        </div>
    </div>
</header>
