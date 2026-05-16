@props([
    'variant' => 'primary',
    'size' => 'md',
    'loading' => false,
    'href' => null,
])

@php
    $base = 'relative inline-flex items-center justify-center font-semibold whitespace-nowrap transition-all active:scale-95 disabled:opacity-50 disabled:pointer-events-none focus:outline-none focus:ring-2 focus:ring-offset-2';

    $variants = [
        'primary' => 'bg-success text-white rounded-xl shadow-sm hover:bg-success-600 focus:ring-success dark:bg-success-600 dark:hover:bg-success-700',
        'secondary' => 'bg-primary text-white rounded-xl shadow-sm hover:bg-primary-600 focus:ring-primary dark:bg-primary-600',
        'danger' => 'bg-danger text-white rounded-xl shadow-sm hover:bg-danger-600 focus:ring-danger',
        'sos' => 'bg-danger text-white rounded-full shadow-2xl hover:bg-danger-600 focus:ring-danger',
        'ghost' => 'bg-transparent text-gray-600 hover:bg-gray-100 rounded-xl dark:text-gray-300 dark:hover:bg-surface-dark-card',
        'outline' => 'bg-transparent border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-surface-dark-card',
    ];

    $sizes = [
        'sm' => 'px-3 py-1.5 text-sm gap-1.5',
        'md' => 'px-5 py-2.5 text-sm gap-2',
        'lg' => 'px-6 py-3 text-base gap-2',
        'xl' => 'px-8 py-4 text-lg gap-3',
        'sos' => 'w-36 h-36 text-lg',
    ];

    $classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes]) }}
        @if($loading) wire:loading.attr="disabled" wire:target="{{ $loading }}" @endif
    >
        @if($loading)
            <span wire:loading wire:target="{{ $loading }}" class="absolute inset-0 flex items-center justify-center">
                <x-icon name="loader-2" class="w-5 h-5 animate-spin" />
            </span>
            <span wire:loading.remove wire:target="{{ $loading }}">
                {{ $slot }}
            </span>
        @else
            {{ $slot }}
        @endif
    </button>
@endif
