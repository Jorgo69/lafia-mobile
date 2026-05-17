<x-mobile-page :title="__('nav.tips')" :back="true">

    {{-- Filtres catégories --}}
    <div class="flex gap-2 overflow-x-auto scroll-hide snap-x mb-5 pb-1 -mx-4 px-4">
        <x-chip wire:click="setCategory('')" :active="$category === ''">
            {{ __('common.all') }}
        </x-chip>
        @foreach($categories as $cat)
            <x-chip
                wire:click="setCategory('{{ $cat->value }}')"
                :active="$category === $cat->value"
                :activeClass="$cat->chipClass()"
            >
                <x-icon :name="$cat->icon()" class="w-4 h-4 shrink-0" />
                {{ $cat->label() }}
            </x-chip>
        @endforeach
    </div>

    {{-- Liste des conseils --}}
    <div class="space-y-3 list-fade-in" wire:loading.class="opacity-50">
        @forelse($tips as $tip)
            @php $cat = $tip->category; @endphp
            <div
                x-data="{ open: false }"
                class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden"
            >
                <button
                    @click="open = !open"
                    class="w-full flex items-start gap-3 px-4 py-3.5 press-feedback text-left"
                >
                    <div class="w-9 h-9 rounded-xl {{ $cat->colorClass() }} flex items-center justify-center shrink-0 mt-0.5">
                        <x-icon :name="$cat->icon()" class="w-5 h-5" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight">{{ $tip->title }}</p>
                            @if($tip->is_pinned)
                                <x-icon name="bookmark" class="w-3.5 h-3.5 text-primary shrink-0" />
                            @endif
                        </div>
                        <p class="text-2xs text-gray-400 mt-0.5">{{ $cat->label() }}</p>
                    </div>
                    <x-icon name="chevron-down" class="w-4 h-4 text-gray-400 shrink-0 mt-1 transition-transform" x-bind:class="open ? 'rotate-180' : ''" />
                </button>

                <div x-show="open" x-cloak x-collapse class="px-4 pb-4 pt-0">
                    <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $tip->content }}</p>
                        @if($tip->source)
                            <p class="text-2xs text-gray-400 mt-2">Source : {{ $tip->source }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <x-icon name="lightbulb" class="w-10 h-10 mx-auto text-gray-300 mb-3" />
                <p class="text-sm text-gray-400">Aucun conseil disponible</p>
            </div>
        @endforelse
    </div>

</x-mobile-page>
