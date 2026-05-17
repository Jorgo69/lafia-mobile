@props(['id', 'name', 'category', 'shortCode', 'phone'])

@php
    $cat = \App\Modules\Emergency\Enums\EmergencyCategory::tryFrom($category);
    $iconName = $cat?->icon() ?? 'phone';
    $colorClass = $cat?->iconColorClass() ?? 'text-gray-600 bg-gray-100';
@endphp

<div class="relative w-full rounded-2xl bg-surface border border-gray-100 shadow-sm
            hover:shadow-md transition-all dark:bg-surface-dark-card dark:border-gray-700">
    {{-- Report button --}}
    <div class="absolute top-1.5 right-1.5 z-10">
        <x-report-button
            target-type="emergency_center"
            :target-id="$id"
            :target-label="$name"
        />
    </div>

    {{-- Call zone --}}
    <a
        href="tel:{{ $phone }}"
        @click.throttle.1000ms
        class="w-full flex flex-col items-center gap-2 py-4 min-h-[88px] press-feedback"
    >
        <div class="w-12 h-12 rounded-full flex items-center justify-center {{ $colorClass }}">
            <x-icon :name="$iconName" class="w-6 h-6" />
        </div>
        <span class="text-sm font-semibold text-surface-dark dark:text-gray-200">{{ $name }}</span>
        <span class="text-lg font-bold text-danger">{{ $shortCode }}</span>
    </a>
</div>
