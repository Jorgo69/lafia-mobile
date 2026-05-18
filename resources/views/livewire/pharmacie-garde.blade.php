@php use App\Livewire\Enums\PharmacyViewMode; @endphp
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
            :options="collect($viewModes)->mapWithKeys(fn($m) => [$m->value => $m->label()])->toArray()"
            :active="$viewMode->value"
            wireClick="setViewMode"
        />

        {{-- DE GARDE --}}
        @if($viewMode === PharmacyViewMode::GUARD)
            @if($this->onGuard->isNotEmpty())
                <div class="bg-success-50 dark:bg-success-900/20 rounded-2xl p-3 border border-success-200 dark:border-success-800">
                    <div class="text-xs font-semibold text-success-700 dark:text-success-400">
                        {{ __('pharma.this_week') }} — {{ now()->startOfWeek()->format('d/m') }} au {{ now()->endOfWeek()->format('d/m/Y') }}
                    </div>
                    <div class="text-xs text-success-600 dark:text-success-500 mt-0.5">
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
        @elseif($viewMode === PharmacyViewMode::ALL)
            <div class="relative">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="searchQuery"
                    placeholder="{{ __('pharma.search') }}"
                    class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 pl-10 pr-4 py-3 text-sm focus:ring-2 focus:ring-success-500 focus:border-transparent"
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
        @elseif($viewMode === PharmacyViewMode::NEAREST)
            @if($userLat === null)
                <div class="text-center py-12 text-gray-400">
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

    {{-- Dialogue de consentement : Localisation --}}
    @if($showLocationRationale)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-end justify-center">
            <div class="bg-white dark:bg-gray-900 w-full max-w-md rounded-t-3xl px-6 pt-6 pb-safe-tab">

                <div class="w-10 h-1 bg-gray-300 dark:bg-gray-600 rounded-full mx-auto mb-5"></div>

                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                        <x-icon name="map-pin" class="w-5 h-5 text-primary-600" />
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ __('pharma.location_permission_title') }}</h3>
                </div>

                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    {{ __('pharma.location_permission_desc') }}
                </p>

                <ul class="space-y-2 mb-5">
                    <li class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <x-icon name="check-circle" class="w-4 h-4 text-success-500 flex-shrink-0 mt-0.5" />
                        {{ __('pharma.location_perm_point1') }}
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <x-icon name="check-circle" class="w-4 h-4 text-success-500 flex-shrink-0 mt-0.5" />
                        {{ __('pharma.location_perm_point2') }}
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300 font-medium">
                        <x-icon name="shield-check" class="w-4 h-4 text-primary-500 flex-shrink-0 mt-0.5" />
                        {{ __('pharma.location_perm_point3') }}
                    </li>
                </ul>

                <div class="flex gap-3">
                    <x-btn variant="ghost" wire:click="cancelLocationRequest" class="flex-1">
                        {{ __('common.cancel') }}
                    </x-btn>
                    <x-btn variant="primary" wire:click="confirmLocationRequest" class="flex-1">
                        <x-icon name="map-pin" class="w-4 h-4" />
                        {{ __('common.authorize') }}
                    </x-btn>
                </div>

            </div>
        </div>
    @endif

</x-mobile-page>

@script
<script>
    $wire.on('request-location', () => {
        if (!navigator.geolocation) {
            $wire.handleLocationDenied();
            return;
        }
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                $wire.setUserLocation(pos.coords.latitude, pos.coords.longitude);
            },
            () => {
                $wire.handleLocationDenied();
            },
            { timeout: 15000, maximumAge: 60000 }
        );
    });
</script>
@endscript
