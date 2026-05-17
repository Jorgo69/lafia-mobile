@props(['id', 'name', 'department', 'phone', 'operator'])

<div class="relative w-full flex items-center justify-between p-4 rounded-2xl bg-surface border border-gray-100 shadow-sm
            hover:shadow-md transition-all dark:bg-surface-dark-card dark:border-gray-700">
    <div class="flex items-center gap-3 text-left flex-1 min-w-0">
        <div class="w-10 h-10 rounded-full bg-success-light dark:bg-success/20 flex items-center justify-center shrink-0">
            <x-icon name="map-pin" class="w-5 h-5 text-success" />
        </div>
        <div class="min-w-0">
            <div class="font-semibold text-sm text-surface-dark dark:text-gray-200 truncate">{{ $name }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $department }}</div>
        </div>
    </div>
    <div class="flex items-center gap-1.5 shrink-0">
        <x-report-button
            target-type="emergency_center"
            :target-id="$id"
            :target-label="$name"
        />
        <button
            wire:click="callNumber('{{ $phone }}')"
            @click.throttle.1000ms
            class="bg-success-500 text-white p-2 rounded-xl press-feedback"
        >
            <x-icon name="phone" class="w-5 h-5" />
        </button>
    </div>
</div>
