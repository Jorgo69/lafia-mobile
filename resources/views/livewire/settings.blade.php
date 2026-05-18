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

        {{-- Autorisations --}}
        <section>
            <h3 class="text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider mb-3">{{ __('common.permissions') }}</h3>
            <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden divide-y divide-gray-100 dark:divide-gray-700">

                {{-- Appel direct --}}
                <button
                    wire:click="toggleCallMode"
                    class="w-full flex items-center justify-between px-4 py-3.5 press-feedback"
                >
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-danger-100 dark:bg-danger-900/30 flex items-center justify-center shrink-0">
                            <x-icon name="phone" class="w-4 h-4 text-danger-600" />
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ __('common.direct_call') }}</span>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $directCall ? __('common.direct_call_on') : __('common.direct_call_off') }}</p>
                        </div>
                    </div>
                    <div role="switch" aria-checked="{{ $directCall ? 'true' : 'false' }}" class="w-11 h-6 rounded-full transition-colors {{ $directCall ? 'bg-danger-500' : 'bg-gray-300' }} relative shrink-0 ml-3">
                        <div class="absolute top-0.5 {{ $directCall ? 'right-0.5' : 'left-0.5' }} w-5 h-5 bg-white rounded-full shadow transition-all"></div>
                    </div>
                </button>

                {{-- Localisation --}}
                <button
                    wire:click="toggleLocationConsent"
                    class="w-full flex items-center justify-between px-4 py-3.5 press-feedback"
                >
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center shrink-0">
                            <x-icon name="map-pin" class="w-4 h-4 text-primary-600" />
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ __('common.location_permission') }}</span>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $locationConsent ? __('common.location_permission_on') : __('common.location_permission_off') }}</p>
                        </div>
                    </div>
                    <div role="switch" aria-checked="{{ $locationConsent ? 'true' : 'false' }}" class="w-11 h-6 rounded-full transition-colors {{ $locationConsent ? 'bg-primary-500' : 'bg-gray-300' }} relative shrink-0 ml-3">
                        <div class="absolute top-0.5 {{ $locationConsent ? 'right-0.5' : 'left-0.5' }} w-5 h-5 bg-white rounded-full shadow transition-all"></div>
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
                    <div role="switch" aria-checked="{{ $lockEnabled ? 'true' : 'false' }}" class="w-11 h-6 rounded-full transition-colors {{ $lockEnabled ? 'bg-secondary-500' : 'bg-gray-300' }} relative shrink-0 ml-3">
                        <div class="absolute top-0.5 {{ $lockEnabled ? 'right-0.5' : 'left-0.5' }} w-5 h-5 bg-white rounded-full shadow transition-all"></div>
                    </div>
                </button>

                {{-- Interval (only if lock enabled) --}}
                @if($lockEnabled)
                    @foreach($lockIntervals as $interval)
                        <button
                            wire:click="setLockInterval('{{ $interval->value }}')"
                            class="w-full flex items-center justify-between px-4 py-3.5 min-h-11 press-feedback"
                        >
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $interval->label() }}</span>
                            @if($lockInterval === $interval)
                                <x-icon name="check" class="w-5 h-5 text-secondary-500" />
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
                <div class="mb-3 bg-success-50 dark:bg-success-900/20 border border-success-200 dark:border-success-800 rounded-2xl px-4 py-3">
                    <p class="text-sm text-success-700 dark:text-success-400 font-medium">{{ __('common.rollback_success') }}</p>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($datasets as $ds)
                    <div class="flex items-center justify-between px-4 py-3">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $ds['label'] }}</p>
                            <p class="text-2xs text-gray-400">v{{ $ds['version'] }}</p>
                        </div>
                        @if($ds['has_snapshot'])
                            <button
                                wire:click="rollbackResource('{{ $ds['key'] }}')"
                                wire:confirm="{{ __('common.rollback') }} {{ $ds['label'] }} ?"
                                class="text-xs font-semibold text-warning-600 dark:text-warning-400 press-feedback px-3 py-1.5"
                            >
                                <x-icon name="undo" class="w-4 h-4 inline -mt-0.5" />
                                {{ __('common.rollback') }}
                            </button>
                        @else
                            <span class="text-2xs text-gray-400">{{ __('common.no_snapshot') }}</span>
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
                    <span class="text-sm font-medium text-success-600">{{ __('vault.encrypted_info') }}</span>
                </div>
            </div>
        </section>

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
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ __('common.location_permission_title') }}</h3>
                </div>

                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    {{ __('common.location_permission_desc') }}
                </p>

                <ul class="space-y-2 mb-5">
                    <li class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <x-icon name="check-circle" class="w-4 h-4 text-success-500 flex-shrink-0 mt-0.5" />
                        {{ __('common.location_perm_point1') }}
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <x-icon name="check-circle" class="w-4 h-4 text-success-500 flex-shrink-0 mt-0.5" />
                        {{ __('common.location_perm_point2') }}
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300 font-medium">
                        <x-icon name="shield-check" class="w-4 h-4 text-primary-500 flex-shrink-0 mt-0.5" />
                        {{ __('common.location_perm_point3') }}
                    </li>
                </ul>

                <div class="flex gap-3">
                    <x-btn variant="ghost" wire:click="cancelLocationConsent" class="flex-1">
                        {{ __('common.cancel') }}
                    </x-btn>
                    <x-btn variant="primary" wire:click="confirmLocationConsent" class="flex-1">
                        <x-icon name="map-pin" class="w-4 h-4" />
                        {{ __('common.authorize') }}
                    </x-btn>
                </div>

            </div>
        </div>
    @endif

    {{-- Dialogue de consentement : Appel direct --}}
    @if($showDirectCallRationale)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-end justify-center">
            <div class="bg-white dark:bg-gray-900 w-full max-w-md rounded-t-3xl px-6 pt-6 pb-safe-tab">

                <div class="w-10 h-1 bg-gray-300 dark:bg-gray-600 rounded-full mx-auto mb-5"></div>

                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-danger-100 dark:bg-danger-900/30 flex items-center justify-center flex-shrink-0">
                        <x-icon name="phone" class="w-5 h-5 text-danger-600" />
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ __('common.direct_call_permission_title') }}</h3>
                </div>

                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    {{ __('common.direct_call_permission_desc') }}
                </p>

                <ul class="space-y-2 mb-5">
                    <li class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <x-icon name="check-circle" class="w-4 h-4 text-success-500 flex-shrink-0 mt-0.5" />
                        {{ __('common.direct_call_perm_point1') }}
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <x-icon name="check-circle" class="w-4 h-4 text-success-500 flex-shrink-0 mt-0.5" />
                        {{ __('common.direct_call_perm_point2') }}
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300 font-medium">
                        <x-icon name="shield-check" class="w-4 h-4 text-primary-500 flex-shrink-0 mt-0.5" />
                        {{ __('common.direct_call_perm_point3') }}
                    </li>
                </ul>

                <div class="flex gap-3">
                    <x-btn variant="ghost" wire:click="cancelDirectCall" class="flex-1">
                        {{ __('common.cancel') }}
                    </x-btn>
                    <x-btn variant="danger" wire:click="confirmDirectCall" class="flex-1">
                        <x-icon name="phone" class="w-4 h-4" />
                        {{ __('common.authorize') }}
                    </x-btn>
                </div>

            </div>
        </div>
    @endif

</x-mobile-page>

@script
<script>
    $wire.on('request-call-permission', () => {
        window.location.href = 'tel-direct:permission-check';
    });
    window.addEventListener('call-permission-result', (e) => {
        $wire.handleCallPermissionResult(e.detail.granted);
    });

    $wire.on('request-location-permission', () => {
        if (!navigator.geolocation) {
            $wire.handleLocationPermissionResult(false);
            return;
        }
        navigator.geolocation.getCurrentPosition(
            () => { $wire.handleLocationPermissionResult(true); },
            () => { $wire.handleLocationPermissionResult(false); },
            { timeout: 10000, maximumAge: 0 }
        );
    });
</script>
@endscript
