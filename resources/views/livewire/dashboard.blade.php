<x-mobile-page title="Lafia">
    <div class="space-y-5">

        {{-- SOS Button --}}
        <div class="flex justify-center py-3">
            <button
                wire:click="callNumber('118')"
                x-on:click.throttle.2000ms
                class="relative w-32 h-32 rounded-full bg-gradient-to-b from-red-500 to-red-600 text-white shadow-xl shadow-red-500/30 press-feedback flex items-center justify-center"
            >
                <div class="absolute inset-0 rounded-full animate-pulse bg-red-400 opacity-15"></div>
                <div class="text-center relative">
                    <x-icon name="phone-call" class="w-9 h-9 mx-auto mb-1" />
                    <div class="text-xl font-black">{{ __('dashboard.sos_label') }}</div>
                    <div class="text-[10px] opacity-80">118</div>
                </div>
            </button>
        </div>

        {{-- Category Filters --}}
        <x-scroll-row>
            <x-chip
                :active="$selectedCategory === ''"
                wire:click="filterByCategory('')"
            >
                {{ __('common.all') }}
            </x-chip>
            @foreach (\App\Modules\Emergency\Enums\EmergencyCategory::cases() as $cat)
                @if(!in_array($cat, [\App\Modules\Emergency\Enums\EmergencyCategory::CIVIL_PROTECTION, \App\Modules\Emergency\Enums\EmergencyCategory::GENERAL]))
                <x-chip
                    :active="$selectedCategory === $cat->value"
                    wire:click="filterByCategory('{{ $cat->value }}')"
                >
                    <x-icon :name="$cat->icon()" class="w-4 h-4" />
                    {{ $cat->label() }}
                </x-chip>
                @endif
            @endforeach
        </x-scroll-row>

        {{-- National Emergency Services --}}
        <section>
            <x-section-header audio-key="dashboard.services">{{ __('dashboard.services') }}</x-section-header>
            <div class="grid grid-cols-2 gap-3 list-fade-in" wire:loading.class="opacity-50" wire:target="filterByCategory">
                @forelse ($nationalServices as $service)
                    <x-emergency-card
                        :id="$service['id']"
                        :name="$service['name']"
                        :category="$service['category']"
                        :short-code="$service['short_code']"
                        :phone="$service['phone']"
                    />
                @empty
                    <x-skeleton :lines="2" />
                    <x-skeleton :lines="2" />
                @endforelse
            </div>
        </section>

        {{-- Operator Selector --}}
        <section>
            <x-section-header audio-key="dashboard.operator">{{ __('dashboard.operator_filter') }}</x-section-header>
            <x-segment
                :options="['mtn' => 'MTN', 'moov' => 'Moov', 'celtiis' => 'Celtiis']"
                :active="$userOperator"
                wireClick="setOperator"
            />
        </section>

        {{-- Tips --}}
        @if($this->tips->isNotEmpty())
        <section>
            <x-section-header>{{ __('dashboard.tips') }}</x-section-header>
            <x-scroll-row>
                @foreach($this->tips as $tip)
                    @php
                        $colors = [
                            'blue' => 'border-blue-200 bg-blue-50 dark:border-blue-800 dark:bg-blue-900/20',
                            'yellow' => 'border-yellow-200 bg-yellow-50 dark:border-yellow-800 dark:bg-yellow-900/20',
                            'green' => 'border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-900/20',
                            'red' => 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20',
                            'purple' => 'border-purple-200 bg-purple-50 dark:border-purple-800 dark:bg-purple-900/20',
                        ];
                        $color = $colors[$tip->category->color()] ?? $colors['blue'];
                    @endphp
                    <div class="flex-shrink-0 w-64 snap-start rounded-2xl border p-3 {{ $color }}">
                        <div class="flex items-center gap-2 mb-1.5">
                            <x-icon :name="$tip->category->icon()" class="w-4 h-4 opacity-70" />
                            <span class="text-[10px] font-bold uppercase opacity-60">{{ $tip->category->label() }}</span>
                        </div>
                        <h4 class="font-bold text-sm">{{ $tip->title }}</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 line-clamp-3">{{ $tip->content }}</p>
                    </div>
                @endforeach
            </x-scroll-row>
        </section>
        @endif

        {{-- Local CCPC Centers --}}
        <section class="pb-2">
            <x-section-header audio-key="dashboard.centers">{{ __('dashboard.nearest_center') }}</x-section-header>
            <div class="space-y-2 list-fade-in" wire:loading.class="opacity-50" wire:target="setOperator">
                @forelse ($localCenters as $center)
                    <x-center-row
                        :id="$center['id']"
                        :name="$center['name']"
                        :department="$center['department']"
                        :phone="$center['phone']"
                        :operator="$center['operator']"
                    />
                @empty
                    @for ($i = 0; $i < 3; $i++)
                        <x-card><x-skeleton :lines="2" avatar /></x-card>
                    @endfor
                @endforelse
            </div>
        </section>

    </div>
</x-mobile-page>

@script
<script>
    $wire.on('initiate-call', ({ number }) => {
        window.location.href = `tel:${number}`;
    });
</script>
@endscript
