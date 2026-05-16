@props([
    'options' => [],
    'active' => '',
    'wireClick' => '',
])

<div class="flex bg-gray-100 dark:bg-gray-800 rounded-xl p-1 gap-1">
    @foreach($options as $value => $label)
        <button
            wire:click="{{ $wireClick }}('{{ $value }}')"
            class="flex-1 py-2.5 rounded-lg text-sm font-medium transition-all press-feedback
                {{ $active === (string)$value
                    ? 'bg-white dark:bg-gray-700 shadow-sm text-gray-900 dark:text-white'
                    : 'text-gray-500 dark:text-gray-400' }}"
        >
            {{ $label }}
        </button>
    @endforeach
</div>
