@props(['variant' => 'default'])

@php
    $variants = [
        'default' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
        'success' => 'bg-success-100 text-success-700 dark:bg-success-700/20 dark:text-success-100',
        'danger' => 'bg-danger-100 text-danger-700 dark:bg-danger-700/20 dark:text-danger-100',
        'warning' => 'bg-warning-100 text-warning-600 dark:bg-warning-600/20 dark:text-warning-100',
        'primary' => 'bg-primary-100 text-primary-700 dark:bg-primary-700/20 dark:text-primary-100',
        'active' => 'bg-green-100 text-green-700 dark:bg-green-700/20 dark:text-green-300',
        'lost' => 'bg-red-100 text-red-700 dark:bg-red-700/20 dark:text-red-300',
    ];
@endphp

<span {{ $attributes->merge([
    'class' => 'inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium ' . ($variants[$variant] ?? $variants['default'])
]) }}>
    {{ $slot }}
</span>
