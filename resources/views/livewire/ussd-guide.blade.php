<x-mobile-page :title="__('ussd.title')" x-data="{}" @launch-ussd.window="window.open($event.detail.uri, '_self')">
    <div class="space-y-4">

        {{-- Operator selector --}}
        <div class="flex gap-2">
            @foreach($operators as $op)
                <x-chip
                    :active="$activeOperator === $op->value"
                    :color="$op->color()"
                    wire:click="setOperator('{{ $op->value }}')"
                    class="flex-1 justify-center"
                >
                    {{ $op->label() }}
                </x-chip>
            @endforeach
        </div>

        {{-- Tabs --}}
        <x-segment
            :options="['list' => __('ussd.list_mode'), 'favorites' => __('ussd.favorites')]"
            :active="$activeTab"
            wireClick="setTab"
        />

        {{-- Recents --}}
        @if($this->recent->isNotEmpty() && $activeTab === 'list')
            <x-scroll-row gap="2">
                @foreach($this->recent as $code)
                    <button
                        wire:click="selectCode({{ $code->id }})"
                        class="flex-shrink-0 snap-start bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-xl px-3 py-2 text-sm font-medium text-blue-700 dark:text-blue-300 press-feedback"
                    >
                        {{ $code->label }}
                    </button>
                @endforeach
            </x-scroll-row>
        @endif

        @if($activeTab === 'list')
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
                                <div class="text-xs font-mono text-blue-600 dark:text-blue-400 mt-1">{{ $code->code }}</div>
                            </button>
                            <button
                                wire:click="toggleFavorite({{ $code->id }})"
                                class="p-2 press-feedback {{ in_array($code->id, $this->favoriteIds) ? 'text-yellow-500' : 'text-gray-300 dark:text-gray-600' }}"
                            >
                                <x-icon name="star" class="w-5 h-5" />
                            </button>
                            <button wire:click="selectCode({{ $code->id }})" class="bg-blue-500 text-white p-2.5 rounded-xl press-feedback">
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

        @elseif($activeTab === 'favorites')
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
                            <button wire:click="selectCode({{ $fav->ussd_code_id }})" class="bg-blue-500 text-white p-2.5 rounded-xl press-feedback">
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
            x-on:touchmove="if(dragging) { dy = $event.touches[0].clientY - startY; if(dy > 0) $el.style.transform = `translateY(${dy}px)` }"
            x-on:touchend="dragging = false; if(dy > 100) $wire.cancelGuided(); else $el.style.transform = ''; dy = 0"
        >
            <div class="sheet-handle"></div>
            <div class="px-5 pb-5 space-y-4 overflow-y-auto" style="max-height: 75vh">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-lg">{{ $activeCode->label }}</h3>
                    <button wire:click="cancelGuided" class="p-1 press-feedback">
                        <x-icon name="x-mark" class="w-5 h-5 text-gray-400" />
                    </button>
                </div>

                @if($activeCode->description)
                    <p class="text-sm text-gray-500">{{ $activeCode->description }}</p>
                @endif

                @if($activeCode->steps)
                    <div class="space-y-2">
                        @foreach($activeCode->steps as $i => $step)
                            <div class="flex items-start gap-2">
                                <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 text-xs font-bold flex items-center justify-center">{{ $i + 1 }}</span>
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
                                    class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                />
                            </div>
                        @endforeach
                    </div>
                    <button wire:click="launchGuided" class="w-full bg-blue-500 text-white font-bold py-4 rounded-xl press-feedback flex items-center justify-center gap-2">
                        <x-icon name="phone" class="w-5 h-5" />
                        {{ __('ussd.dial') }}
                    </button>
                @else
                    <button wire:click="selectCode({{ $activeCode->id }})" class="w-full bg-blue-500 text-white font-bold py-4 rounded-xl press-feedback flex items-center justify-center gap-2">
                        <x-icon name="phone" class="w-5 h-5" />
                        {{ __('ussd.dial') }} {{ $activeCode->code }}
                    </button>
                @endif

                <div class="text-center">
                    <span class="text-xs font-mono text-gray-400">{{ $activeCode->code }}</span>
                </div>
            </div>
        </div>
    @endif
</x-mobile-page>
