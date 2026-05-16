<x-mobile-page :title="__('pharma.title')">
    <div class="space-y-4">

        {{-- Zone selector --}}
        <x-scroll-row gap="2">
            @foreach($zones as $zone)
                <x-chip
                    :active="$activeZone === $zone->value"
                    wire:click="setZone('{{ $zone->value }}')"
                >
                    {{ $zone->label() }}
                </x-chip>
            @endforeach
        </x-scroll-row>

        {{-- View mode --}}
        <x-segment
            :options="['guard' => __('pharma.on_guard'), 'all' => __('pharma.all'), 'nearest' => __('pharma.nearest')]"
            :active="$viewMode"
            wireClick="setViewMode"
        />

        {{-- DE GARDE --}}
        @if($viewMode === 'guard')
            @if($this->onGuard->isNotEmpty())
                <div class="bg-green-50 dark:bg-green-900/20 rounded-2xl p-3 border border-green-200 dark:border-green-800">
                    <div class="text-xs font-semibold text-green-700 dark:text-green-400">
                        {{ __('pharma.this_week') }} — {{ now()->startOfWeek()->format('d/m') }} au {{ now()->endOfWeek()->format('d/m/Y') }}
                    </div>
                    <div class="text-xs text-green-600 dark:text-green-500 mt-0.5">
                        {{ $this->onGuard->count() }} {{ __('pharma.pharmacies') }}
                    </div>
                </div>
                <div class="space-y-2">
                    @foreach($this->onGuard as $pharmacy)
                        @include('livewire.partials.pharmacy-card', ['pharmacy' => $pharmacy, 'badge' => __('pharma.on_guard')])
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-gray-400">
                    <x-icon name="building-storefront" class="w-10 h-10 mx-auto mb-2 opacity-50" />
                    <p class="text-sm">{{ __('pharma.no_guard') }}</p>
                    <p class="text-xs mt-1 opacity-70">{{ __('pharma.updated_weekly') }}</p>
                </div>
            @endif

            @if($this->nextWeekGuards->isNotEmpty())
                <div class="mt-2">
                    <x-section-header :title="__('pharma.next_week')" />
                    <div class="space-y-2">
                        @foreach($this->nextWeekGuards as $pharmacy)
                            @include('livewire.partials.pharmacy-card', ['pharmacy' => $pharmacy, 'badge' => __('pharma.next_week')])
                        @endforeach
                    </div>
                </div>
            @endif

        {{-- TOUTES --}}
        @elseif($viewMode === 'all')
            <div class="relative">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="searchQuery"
                    placeholder="{{ __('pharma.search') }}"
                    class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 pl-10 pr-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent"
                />
                <x-icon name="magnifying-glass" class="w-5 h-5 absolute left-3 top-3.5 text-gray-400" />
            </div>
            <div class="space-y-2 list-fade-in" wire:loading.class="opacity-50" wire:target="searchQuery,setZone">
                @forelse($this->allPharmacies as $pharmacy)
                    @include('livewire.partials.pharmacy-card', [
                        'pharmacy' => $pharmacy,
                        'badge' => $pharmacy->isOnGuardToday() ? __('pharma.on_guard') : null
                    ])
                @empty
                    <div class="text-center py-12 text-gray-400">
                        <p class="text-sm">{{ __('pharma.no_results') }}</p>
                    </div>
                @endforelse
            </div>

        {{-- PROCHES --}}
        @elseif($viewMode === 'nearest')
            @if($userLat === null)
                <div
                    x-data
                    x-init="
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(
                                (pos) => $wire.setUserLocation(pos.coords.latitude, pos.coords.longitude),
                                () => {}
                            );
                        }
                    "
                    class="text-center py-12 text-gray-400"
                >
                    <x-icon name="map-pin" class="w-10 h-10 mx-auto mb-2 opacity-50 animate-pulse" />
                    <p class="text-sm">{{ __('pharma.enable_location') }}</p>
                </div>
            @elseif($this->nearest->isNotEmpty())
                <div class="space-y-2">
                    @foreach($this->nearest as $item)
                        @include('livewire.partials.pharmacy-card', [
                            'pharmacy' => $item['pharmacy'],
                            'badge' => __('pharma.on_guard'),
                            'distance' => $item['distance'],
                        ])
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-gray-400">
                    <x-icon name="map-pin" class="w-10 h-10 mx-auto mb-2 opacity-50" />
                    <p class="text-sm">{{ __('pharma.no_nearby') }}</p>
                </div>
            @endif
        @endif

    </div>
</x-mobile-page>
