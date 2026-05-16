<x-mobile-page :title="__('circle.title')">
    @if(!$authUnlocked)
        <x-auth-gate />
    @else
    <div class="space-y-5">

        @if (!$identity)
            <div class="text-center py-16 space-y-4">
                <div class="w-20 h-20 mx-auto rounded-full bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center">
                    <x-icon name="shield-check" class="w-10 h-10 text-indigo-500" />
                </div>
                <h2 class="text-lg font-bold">{{ __('circle.title') }}</h2>
                <p class="text-sm text-gray-500 max-w-xs mx-auto">{{ __('circle.recovery_info') }}</p>
                <x-btn variant="primary" wire:click="registerDevice" loading="registerDevice">
                    <x-icon name="smartphone" class="w-4 h-4 shrink-0" /> {{ __('common.save') }}
                </x-btn>
            </div>

        @else
            {{-- Device Info --}}
            <x-card>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                        <x-icon name="smartphone" class="w-5 h-5 text-green-600" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-sm truncate">{{ $identity['device_name'] ?? __('circle.my_device') }}</div>
                        <div class="text-[11px] text-gray-400 font-mono">{{ substr($identity['fingerprint'], 0, 16) }}...</div>
                    </div>
                    @php $deviceStatus = \App\Modules\Identity\Enums\DeviceStatus::tryFrom($identity['status']); @endphp
                    <span class="text-[11px] font-bold px-2 py-1 rounded-full {{ $deviceStatus?->badgeClass() ?? '' }}">
                        {{ $deviceStatus?->label() ?? $identity['status'] }}
                    </span>
                </div>
            </x-card>

            {{-- Guardians --}}
            <section>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-sm">
                        {{ __('circle.guardians') }} ({{ count($guardians) }}/{{ $identity['threshold'] + 1 }})
                    </h3>
                    @if(count($guardians) < $identity['threshold'] + 1)
                        <button wire:click="$toggle('showAddGuardian')" class="text-sm text-indigo-500 font-medium flex items-center gap-1 press-feedback">
                            <x-icon name="user-plus" class="w-4 h-4" /> {{ __('circle.add_guardian') }}
                        </button>
                    @endif
                </div>

                @if ($showAddGuardian)
                    <x-card class="mb-3 space-y-3">
                        <input
                            type="text"
                            wire:model="guardianAlias"
                            placeholder="{{ __('circle.placeholder_alias') }}"
                            class="w-full rounded-2xl border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-3"
                        >
                        <div class="flex gap-2">
                            <x-btn variant="primary" wire:click="addGuardian" class="flex-1" size="sm" loading="addGuardian">
                                {{ __('circle.add_guardian') }}
                            </x-btn>
                            <x-btn variant="outline" wire:click="$set('showAddGuardian', false)" class="flex-1" size="sm">
                                {{ __('common.cancel') }}
                            </x-btn>
                        </div>
                    </x-card>
                @endif

                <div class="space-y-2">
                    @forelse ($guardians as $guardian)
                        <x-guardian-card
                            :alias="$guardian['alias']"
                            :status="$guardian['status']"
                            :status-label="$guardian['status_label']"
                            :accepted-at="$guardian['accepted_at']"
                        />
                    @empty
                        <div class="text-center py-8 text-gray-400">
                            <x-icon name="users" class="w-8 h-8 mx-auto mb-2 opacity-50" />
                            <p class="text-sm">{{ __('circle.no_guardians') }}</p>
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Recovery --}}
            <section>
                <h3 class="font-semibold text-sm mb-3">{{ __('circle.recovery') }}</h3>

                @if ($activeRecovery)
                    <x-card class="border-amber-300 border-2 space-y-3">
                        <div class="flex items-center gap-2">
                            <x-icon name="timer" class="w-5 h-5 text-amber-500" />
                            <span class="font-semibold text-sm text-amber-600">{{ __('circle.recovery') }}</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="flex-1 bg-gray-100 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                                <div class="bg-amber-500 h-full rounded-full transition-all"
                                     style="width: {{ ($activeRecovery['fragments_received'] / max($activeRecovery['fragments_needed'], 1)) * 100 }}%"></div>
                            </div>
                            <span class="text-sm font-bold">{{ $activeRecovery['fragments_received'] }}/{{ $activeRecovery['fragments_needed'] }}</span>
                        </div>
                    </x-card>
                @else
                    <x-card class="text-center py-6 space-y-3">
                        <x-icon name="smartphone-nfc" class="w-8 h-8 mx-auto text-gray-300" />
                        @if (count($guardians) >= $identity['threshold'])
                            <button wire:click="$toggle('showRecovery')" class="text-sm text-amber-600 font-semibold press-feedback">
                                {{ __('circle.request_recovery') }}
                            </button>
                            @if ($showRecovery)
                                <div class="space-y-2">
                                    <p class="text-xs text-gray-400">{{ __('circle.threshold', ['count' => $identity['threshold']]) }}</p>
                                    <x-btn variant="danger" wire:click="requestRecovery" class="w-full" size="sm" loading="requestRecovery">
                                        <x-icon name="key" class="w-4 h-4" /> {{ __('common.confirm') }}
                                    </x-btn>
                                </div>
                            @endif
                        @else
                            <p class="text-xs text-gray-400">{{ __('circle.threshold', ['count' => $identity['threshold'] - count($guardians)]) }}</p>
                        @endif
                    </x-card>
                @endif
            </section>

        @endif

    </div>
    @endif
</x-mobile-page>
