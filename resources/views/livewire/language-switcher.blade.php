<div class="flex items-center gap-0.5" x-data>
    @foreach($locales as $code => $label)
        <button
            wire:click="setLocale('{{ $code }}')"
            class="text-[9px] font-bold px-1.5 py-1 rounded transition-colors
                {{ $locale === $code
                    ? 'bg-primary text-white'
                    : 'text-gray-400 dark:text-gray-500 hover:text-gray-600' }}"
        >
            {{ $label }}
        </button>
    @endforeach
</div>
