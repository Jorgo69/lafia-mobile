@props(['padding' => true])

<div {{ $attributes->merge([
    'class' => 'rounded-2xl bg-surface border border-gray-100 shadow-sm dark:bg-surface-dark-card dark:border-gray-700'
        . ($padding ? ' p-5' : '')
]) }}>
    {{ $slot }}
</div>
