@props(['alias', 'status', 'statusLabel', 'acceptedAt' => null])

<x-card class="flex items-center gap-3">
    <div class="w-10 h-10 rounded-full bg-primary-light dark:bg-primary/20 flex items-center justify-center shrink-0">
        <x-icon name="user-check" class="w-5 h-5 text-primary" />
    </div>
    <div class="flex-1">
        <div class="font-semibold text-sm dark:text-gray-200">{{ $alias }}</div>
        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $statusLabel }} &middot; {{ $acceptedAt ?? __('common.pending') }}</div>
    </div>
    @php $guardianStatus = \App\Modules\Identity\Enums\GuardianStatus::tryFrom($status); @endphp
    <div class="w-2.5 h-2.5 rounded-full {{ $guardianStatus === \App\Modules\Identity\Enums\GuardianStatus::ACCEPTED ? 'bg-green-500' : 'bg-yellow-500' }}"></div>
</x-card>
