@props(['locked' => true, 'hasData' => false])

<x-card class="flex items-center gap-4">
    <div class="w-12 h-12 rounded-full flex items-center justify-center {{ $locked ? 'bg-success-light dark:bg-success/20' : 'bg-primary-light dark:bg-primary/20' }}">
        @if ($locked)
            <x-icon name="lock" class="w-6 h-6 text-success" />
        @else
            <x-icon name="lock-open" class="w-6 h-6 text-primary" />
        @endif
    </div>
    <div class="flex-1">
        <div class="font-semibold text-sm dark:text-gray-200">
            {{ $locked ? __('vault.status_locked') : __('vault.status_unlocked') }}
        </div>
        <div class="text-xs text-gray-500 dark:text-gray-400">
            {{ $hasData ? __('vault.encrypted_info') : __('vault.no_data') }}
        </div>
    </div>
    {{ $slot }}
</x-card>
