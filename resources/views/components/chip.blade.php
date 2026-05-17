@props([
    'active' => false,
    'activeClass' => 'bg-primary text-white shadow',
])

@php
    $stateClass = $active
        ? $activeClass
        : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400';
@endphp

<button {{ $attributes->merge([
    'class' => "flex-shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-medium press-feedback transition-colors whitespace-nowrap snap-start {$stateClass}",
]) }}>
    {{ $slot }}
</button>
