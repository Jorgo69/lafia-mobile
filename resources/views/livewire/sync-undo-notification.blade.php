<div
    wire:poll.10s="checkForUpdates"
    x-data="{ show: @entangle('visible') }"
    x-show="show"
    x-cloak
    x-transition.opacity.duration.300ms
    class="fixed top-16 inset-x-4 z-50 safe-top"
>
    <div class="max-w-md mx-auto bg-gray-800 dark:bg-gray-700 rounded-2xl px-4 py-3 shadow-lg flex items-center justify-between gap-3">
        <span class="text-sm text-white font-medium">{{ $message }}</span>
        <div class="flex items-center gap-2 shrink-0">
            <button
                wire:click="undoAll"
                class="text-sm font-bold text-warning-400 press-feedback"
            >
                {{ __('common.undo') }}
            </button>
            <button
                wire:click="dismiss"
                class="text-gray-400 press-feedback"
            >
                <x-icon name="x" class="w-4 h-4" />
            </button>
        </div>
    </div>
</div>
