@php use App\Livewire\Enums\UssdTab; @endphp
<x-mobile-page :title="__('ussd.title')">
    <div class="space-y-4">

        {{-- Operator selector --}}
        <div class="flex gap-2">
            @foreach($operators as $op)
                <x-chip
                    :active="$activeOperator === $op->value"
                    :activeClass="$op->chipClass()"
                    wire:click="setOperator('{{ $op->value }}')"
                    class="flex-1 justify-center"
                >
                    {{ $op->label() }}
                </x-chip>
            @endforeach
        </div>

        {{-- Tabs --}}
        <x-segment
            :options="collect($ussdTabs)->mapWithKeys(fn($t) => [$t->value => $t->label()])->toArray()"
            :active="$activeTab->value"
            wireClick="setTab"
        />

        {{-- Recents --}}
        @if($this->recent->isNotEmpty() && $activeTab === UssdTab::LIST)
            <x-scroll-row gap="2">
                @foreach($this->recent as $code)
                    <button
                        wire:click="selectCode({{ $code->id }})"
                        class="flex-shrink-0 snap-start bg-primary-50 dark:bg-primary-900/30 border border-primary-200 dark:border-primary-800 rounded-xl px-3 py-2 text-sm font-medium text-primary-700 dark:text-primary-300 press-feedback"
                    >
                        {{ $code->label }}
                    </button>
                @endforeach
            </x-scroll-row>
        @endif

        @if($activeTab === UssdTab::LIST)
            {{-- Categories --}}
            <x-scroll-row gap="2">
                <x-chip
                    :active="$activeCategory === ''"
                    wire:click="setCategory('')"
                >
                    {{ __('common.all') }}
                </x-chip>
                @foreach($categories as $cat)
                    <x-chip
                        :active="$activeCategory === $cat->value"
                        wire:click="setCategory('{{ $cat->value }}')"
                    >
                        <x-icon name="{{ $cat->icon() }}" class="w-4 h-4" />
                        {{ $cat->label() }}
                    </x-chip>
                @endforeach
            </x-scroll-row>

            {{-- Code list --}}
            <div class="space-y-2 list-fade-in" wire:loading.class="opacity-50" wire:target="setCategory,setOperator">
                @forelse($this->codes as $code)
                    <x-card>
                        <div class="flex items-center gap-3">
                            <button wire:click="selectCode({{ $code->id }})" class="flex-1 text-left press-feedback">
                                <div class="font-semibold text-sm">{{ $code->label }}</div>
                                @if($code->description)
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $code->description }}</div>
                                @endif
                                <div class="text-xs font-mono text-primary-600 dark:text-primary-400 mt-1">{{ $code->code }}</div>
                            </button>
                            <button
                                wire:click="toggleFavorite({{ $code->id }})"
                                class="min-h-11 min-w-11 flex items-center justify-center press-feedback {{ in_array($code->id, $this->favoriteIds) ? 'text-warning-500' : 'text-gray-300 dark:text-gray-600' }}"
                            >
                                <x-icon name="star" class="w-5 h-5" />
                            </button>
                            <button wire:click="selectCode({{ $code->id }})" class="min-h-11 min-w-11 flex items-center justify-center bg-primary-500 text-white rounded-xl press-feedback">
                                <x-icon name="{{ $code->needsParams() ? 'arrow-right' : 'phone' }}" class="w-5 h-5" />
                            </button>
                        </div>
                    </x-card>
                @empty
                    <div class="text-center py-12 text-gray-400">
                        <x-icon name="hashtag" class="w-10 h-10 mx-auto mb-2 opacity-50" />
                        <p class="text-sm">{{ __('ussd.no_results') }}</p>
                    </div>
                @endforelse
            </div>

        @elseif($activeTab === UssdTab::FAVORITES)
            <div class="space-y-2">
                @forelse($this->favorites as $fav)
                    <x-card>
                        <div class="flex items-center gap-3">
                            <button wire:click="selectCode({{ $fav->ussd_code_id }})" class="flex-1 text-left press-feedback">
                                <div class="font-semibold text-sm">{{ $fav->custom_label ?? $fav->ussdCode->label }}</div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs text-gray-500">{{ $fav->use_count }}x</span>
                                    @if($fav->last_used_at)
                                        <span class="text-xs text-gray-400">{{ $fav->last_used_at->diffForHumans() }}</span>
                                    @endif
                                </div>
                            </button>
                            <button wire:click="selectCode({{ $fav->ussd_code_id }})" class="bg-primary-500 text-white p-2.5 rounded-xl press-feedback">
                                <x-icon name="phone" class="w-5 h-5" />
                            </button>
                        </div>
                    </x-card>
                @empty
                    <div class="text-center py-12">
                        <x-icon name="star" class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-600 mb-2" />
                        <p class="text-gray-400 text-sm">{{ __('ussd.no_favorites') }}</p>
                    </div>
                @endforelse
            </div>
        @endif

    </div>

    {{-- Bottom sheet guide --}}
    @if($activeCode)
        <div class="sheet-backdrop" wire:click="cancelGuided"></div>
        <div class="sheet-panel"
            x-data="{ startY: 0, dy: 0, dragging: false }"
            x-on:touchstart="startY = $event.touches[0].clientY; dragging = true"
            x-on:touchmove="if(dragging) { dy = $event.touches[0].clientY - startY; if(dy > 0) $el.style.transform = `translateX(-50%) translateY(${dy}px)` }"
            x-on:touchend="dragging = false; if(dy > 100) $wire.cancelGuided(); else $el.style.transform = 'translateX(-50%)'; dy = 0"
        >
            {{-- Handle --}}
            <div class="sheet-handle"></div>

            {{-- Titre (non-scrollable) --}}
            <div class="flex items-center justify-between px-5 pb-3 flex-shrink-0">
                <h3 class="font-bold text-lg">{{ $activeCode->label }}</h3>
                <button wire:click="cancelGuided" class="p-1 press-feedback">
                    <x-icon name="x-mark" class="w-5 h-5 text-gray-400" />
                </button>
            </div>

            {{-- Corps scrollable --}}
            <div class="px-5 space-y-4 overflow-y-auto flex-1 pb-4">

                @if($activeCode->description)
                    <p class="text-sm text-gray-500">{{ $activeCode->description }}</p>
                @endif

                @if($activeCode->steps)
                    <div class="space-y-2">
                        @foreach($activeCode->steps as $i => $step)
                            <div class="flex items-start gap-2">
                                <span class="flex-shrink-0 w-6 h-6 rounded-full bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400 text-xs font-bold flex items-center justify-center">{{ $i + 1 }}</span>
                                <span class="text-sm">{{ $step }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($activeCode->needsParams())
                    <div class="space-y-3">
                        @foreach($activeCode->getParamDefinitions() as $param)
                            <div>
                                <label class="block text-sm font-medium mb-1">{{ $param['label'] }}</label>
                                <input
                                    type="{{ $param['type'] ?? 'text' }}"
                                    wire:model="paramValues.{{ $param['key'] }}"
                                    placeholder="{{ $param['placeholder'] ?? '' }}"
                                    class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                />
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="text-center">
                    <span class="text-xs font-mono text-gray-400">{{ $activeCode->code }}</span>
                </div>
            </div>

            {{-- Footer sticky avec bouton Composer --}}
            <div class="px-5 py-4 flex-shrink-0 border-t border-gray-100 dark:border-gray-800"
                 style="padding-bottom: calc(1rem + env(safe-area-inset-bottom, 0))">
                @if($activeCode->needsParams())
                    <x-btn variant="secondary" wire:click="launchGuided" class="w-full" size="lg" loading="launchGuided">
                        <x-icon name="phone" class="w-5 h-5" />
                        {{ __('ussd.dial') }}
                    </x-btn>
                @else
                    <x-btn variant="secondary" wire:click="selectCode({{ $activeCode->id }})" class="w-full" size="lg">
                        <x-icon name="phone" class="w-5 h-5" />
                        {{ __('ussd.dial') }} {{ $activeCode->code }}
                    </x-btn>
                @endif
            </div>
        </div>
    @endif
</x-mobile-page>

@script
<script>
    $wire.on('launch-ussd', ({ uri }) => {
        window.location.href = uri;
    });
</script>
@endscript
