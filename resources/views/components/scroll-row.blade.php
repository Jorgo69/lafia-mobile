@props([
    'gap' => '3',
])

<div {{ $attributes->merge(['class' => "flex gap-{$gap} overflow-x-auto scroll-hide pb-1 -mx-4 px-4 snap-x snap-mandatory"]) }}>
    {{ $slot }}
</div>
