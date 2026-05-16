@props([
    'active' => false,
    'color' => null,
])

@php
    $activeClass = $active
        ? ($color ? "text-white shadow" : 'bg-primary text-white shadow')
        : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400';
@endphp

<button {{ $attributes->merge([
    'class' => "flex-shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-medium press-feedback transition-colors whitespace-nowrap snap-start {$activeClass}",
    'style' => $active && $color ? "background-color: {$color}" : '',
]) }}>
    {{ $slot }}
</button>
