<button
    wire:click="toggle"
    onclick="document.documentElement.classList.toggle('dark')"
    class="flex flex-col items-center gap-0.5 px-3 py-1 text-gray-400 dark:text-gray-500 transition-colors press-feedback"
>
    @if ($isDark)
        <x-icon name="sun" class="w-6 h-6" />
        <span class="text-2xs font-semibold">{{ __('common.light') }}</span>
    @else
        <x-icon name="moon" class="w-6 h-6" />
        <span class="text-2xs font-semibold">{{ __('common.dark') }}</span>
    @endif
</button>
