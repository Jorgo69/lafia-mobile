@props([
    'show' => false,
    'title' => '',
    'maxHeight' => '85vh',
])

@if($show)
<div
    x-data="{ startY: 0, currentY: 0, dragging: false }"
    x-on:keydown.escape.window="$wire.set('{{ $attributes->wire('model')->value() ?? 'showSheet' }}', false)"
>
    {{-- Backdrop --}}
    <div
        class="sheet-backdrop"
        wire:click="{{ $attributes->wire('close')->value() ?? '' }}"
        x-on:click="$wire.set('{{ $attributes->wire('model')->value() ?? 'showSheet' }}', false)"
    ></div>

    {{-- Panel --}}
    <div
        class="sheet-panel"
        style="max-height: {{ $maxHeight }}"
        x-on:touchstart="startY = $event.touches[0].clientY; dragging = true"
        x-on:touchmove="if (dragging) { currentY = $event.touches[0].clientY - startY; if (currentY > 0) $el.style.transform = `translateY(${currentY}px)` }"
        x-on:touchend="dragging = false; if (currentY > 100) { $wire.set('{{ $attributes->wire('model')->value() ?? 'showSheet' }}', false) } else { $el.style.transform = '' }; currentY = 0"
    >
        {{-- Drag handle --}}
        <div class="sheet-handle"></div>

        {{-- Title --}}
        @if($title)
            <div class="flex items-center justify-between px-5 pb-3">
                <h3 class="font-bold text-lg">{{ $title }}</h3>
                <button
                    wire:click="{{ $attributes->wire('close')->value() ?? '' }}"
                    x-on:click="$wire.set('{{ $attributes->wire('model')->value() ?? 'showSheet' }}', false)"
                    class="p-1 press-feedback"
                >
                    <x-icon name="x-mark" class="w-5 h-5 text-gray-400" />
                </button>
            </div>
        @endif

        {{-- Content --}}
        <div class="px-5 pb-5 overflow-y-auto" style="max-height: calc({{ $maxHeight }} - 4rem)">
            {{ $slot }}
        </div>
    </div>
</div>
@endif
