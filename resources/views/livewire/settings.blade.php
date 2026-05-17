<x-mobile-page :title="__('common.settings')" :back="true">
    <div class="space-y-6">

        {{-- Langue --}}
        <section>
            <h3 class="text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider mb-3">{{ __('common.language') }}</h3>
            <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($locales as $code => $label)
                    <button
                        wire:click="setLocale('{{ $code }}')"
                        class="w-full flex items-center justify-between px-4 py-3.5 press-feedback"
                    >
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $label }}</span>
                        @if($locale === $code)
                            <x-icon name="check" class="w-5 h-5 text-primary" />
                        @endif
                    </button>
                @endforeach
            </div>
        </section>

        {{-- Apparence --}}
        <section>
            <h3 class="text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider mb-3">{{ __('common.appearance') }}</h3>
            <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden">
                <button
                    wire:click="toggleDarkMode"
                    class="w-full flex items-center justify-between px-4 py-3.5 press-feedback"
                >
                    <div class="flex items-center gap-3">
                        <x-icon name="{{ $isDark ? 'moon' : 'sun' }}" class="w-5 h-5 text-gray-500" />
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ __('common.dark_mode') }}</span>
                    </div>
                    <div role="switch" aria-checked="{{ $isDark ? 'true' : 'false' }}" class="w-11 h-6 rounded-full transition-colors {{ $isDark ? 'bg-primary' : 'bg-gray-300' }} relative">
                        <div class="absolute top-0.5 {{ $isDark ? 'right-0.5' : 'left-0.5' }} w-5 h-5 bg-white rounded-full shadow transition-all"></div>
                    </div>
                </button>
            </div>
        </section>

        {{-- Securite --}}
        <section>
            <h3 class="text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider mb-3">
                <x-icon name="lock" class="w-4 h-4 inline -mt-0.5" /> {{ __('vault.lock_enabled') }}
            </h3>
            <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden divide-y divide-gray-100 dark:divide-gray-700">
                {{-- Toggle --}}
                <button
                    wire:click="toggleLock"
                    class="w-full flex items-center justify-between px-4 py-3.5 press-feedback"
                >
                    <div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ __('vault.lock_enabled') }}</span>
                        <p class="text-xs text-gray-400 mt-0.5">{{ __('vault.lock_enabled_desc') }}</p>
                    </div>
                    <div role="switch" aria-checked="{{ $lockEnabled ? 'true' : 'false' }}" class="w-11 h-6 rounded-full transition-colors {{ $lockEnabled ? 'bg-indigo-500' : 'bg-gray-300' }} relative shrink-0 ml-3">
                        <div class="absolute top-0.5 {{ $lockEnabled ? 'right-0.5' : 'left-0.5' }} w-5 h-5 bg-white rounded-full shadow transition-all"></div>
                    </div>
                </button>

                {{-- Interval (only if lock enabled) --}}
                @if($lockEnabled)
                    @php
                        $intervals = [
                            '0' => __('vault.lock_every_time'),
                            '1' => __('vault.lock_1min'),
                            '5' => __('vault.lock_5min'),
                            '15' => __('vault.lock_15min'),
                        ];
                    @endphp
                    @foreach($intervals as $value => $label)
                        <button
                            wire:click="setLockInterval('{{ $value }}')"
                            class="w-full flex items-center justify-between px-4 py-3.5 min-h-[44px] press-feedback"
                        >
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                            @if($lockInterval === (string) $value)
                                <x-icon name="check" class="w-5 h-5 text-indigo-500" />
                            @endif
                        </button>
                    @endforeach
                @endif
            </div>
        </section>

        {{-- Donnees --}}
        <section>
            <h3 class="text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider mb-3">{{ __('common.data_management') }}</h3>

            @if($showRollbackSuccess)
                <div class="mb-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-2xl px-4 py-3">
                    <p class="text-sm text-green-700 dark:text-green-400 font-medium">{{ __('common.rollback_success') }}</p>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($datasets as $ds)
                    <div class="flex items-center justify-between px-4 py-3">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $ds['label'] }}</p>
                            <p class="text-[11px] text-gray-400">v{{ $ds['version'] }}</p>
                        </div>
                        @if($ds['has_snapshot'])
                            <button
                                wire:click="rollbackResource('{{ $ds['key'] }}')"
                                wire:confirm="{{ __('common.rollback') }} {{ $ds['label'] }} ?"
                                class="text-xs font-semibold text-amber-600 dark:text-amber-400 press-feedback px-3 py-1.5"
                            >
                                <x-icon name="undo" class="w-4 h-4 inline -mt-0.5" />
                                {{ __('common.rollback') }}
                            </button>
                        @else
                            <span class="text-[11px] text-gray-400">{{ __('common.no_snapshot') }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>

        {{-- A propos --}}
        <section>
            <h3 class="text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider mb-3">{{ __('common.about') }}</h3>
            <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden divide-y divide-gray-100 dark:divide-gray-700">
                <div class="flex items-center justify-between px-4 py-3.5">
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('common.version') }}</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $appVersion }}</span>
                </div>
                <div class="flex items-center justify-between px-4 py-3.5">
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('common.licence') }}</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">AGPL-3.0</span>
                </div>
                <div class="flex items-center justify-between px-4 py-3.5">
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('common.data_management') }}</span>
                    <span class="text-sm font-medium text-green-600">{{ __('vault.encrypted_info') }}</span>
                </div>
            </div>
        </section>

    </div>
</x-mobile-page>
