@php $embedded = $this->embedded ?? false; @endphp

@if(!$embedded)
<x-mobile-page :title="__('vault.title')">
    @if(!$authUnlocked)
        <x-auth-gate />
    @else
@endif

<div class="space-y-5">

    @if(!$embedded && !$isLocked)
        <x-slot:actions>
            <button wire:click="lock" class="text-sm text-red-500 font-medium flex items-center gap-1.5 min-h-[44px] px-3 press-feedback rounded-xl">
                <x-icon name="lock" class="w-4 h-4" /> {{ __('vault.lock') }}
            </button>
        </x-slot:actions>
    @endif

    {{-- Lock button for embedded mode --}}
    @if($embedded && !$isLocked)
        <div class="flex justify-end">
            <button wire:click="lock" class="text-sm text-red-500 font-medium flex items-center gap-1.5 min-h-[44px] px-3 press-feedback rounded-xl">
                <x-icon name="lock" class="w-4 h-4" /> {{ __('vault.lock') }}
            </button>
        </div>
    @endif

    {{-- Vault Status --}}
    <x-vault-status :locked="$isLocked" :has-data="(bool)$vaultId">
        @if ($isLocked)
            <x-btn variant="primary" size="sm" wire:click="unlock" loading="unlock">
                {{ $vaultId ? __('vault.open') : __('vault.add') }}
            </x-btn>
        @endif
    </x-vault-status>

    @if (!$isLocked)

        @if ($isEditing)
            <form wire:submit="save" class="space-y-4">

                <x-card class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">{{ __('vault.blood_type') }}</label>
                        <select wire:model="bloodType" class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-3 min-h-[44px]">
                            <option value="">—</option>
                            @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bt)
                                <option value="{{ $bt }}">{{ $bt }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium mb-1">{{ __('vault.weight') }}</label>
                            <input type="number" wire:model="weightKg" step="0.1" class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-3 min-h-[44px]" placeholder="72.5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">{{ __('vault.height') }}</label>
                            <input type="number" wire:model="heightCm" step="0.1" class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-3 min-h-[44px]" placeholder="175">
                        </div>
                    </div>
                </x-card>

                <x-card class="space-y-5">
                    <x-multi-input :label="__('vault.allergies')" :items="$allergies" wire-model="newAllergy" wire-add="addAllergy" wire-remove="removeAllergy" :placeholder="__('vault.placeholder_allergy')" variant="warning" icon="triangle-alert" />
                    <x-multi-input :label="__('vault.medications')" :items="$medications" wire-model="newMedication" wire-add="addMedication" wire-remove="removeMedication" :placeholder="__('vault.placeholder_medication')" variant="primary" icon="pill" />
                    <x-multi-input :label="__('vault.conditions')" :items="$conditions" wire-model="newCondition" wire-add="addCondition" wire-remove="removeCondition" :placeholder="__('vault.placeholder_condition')" variant="success" icon="stethoscope" />
                </x-card>

                <x-card>
                    <label class="block text-sm font-medium mb-1">{{ __('vault.emergency_notes') }}</label>
                    <textarea wire:model="emergencyNotes" rows="3" class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-3 px-4 min-h-[44px]"></textarea>
                </x-card>

                <div class="flex gap-3">
                    <x-btn variant="primary" type="submit" class="flex-1" loading="save">
                        <x-icon name="lock" class="w-4 h-4" /> {{ __('vault.save') }}
                    </x-btn>
                    @if ($vaultId)
                        <x-btn variant="outline" type="button" wire:click="$set('isEditing', false)" class="flex-1">
                            {{ __('common.cancel') }}
                        </x-btn>
                    @endif
                </div>

                <p class="text-xs text-center text-gray-400">{{ __('vault.encrypted_info') }}</p>
            </form>

        @elseif ($decryptedData)
            <x-card class="space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold">{{ __('vault.title') }}</h3>
                    <button wire:click="edit" class="text-sm text-blue-500 font-medium flex items-center gap-1 min-h-[44px] px-2 press-feedback rounded-xl">
                        <x-icon name="pencil" class="w-3.5 h-3.5" />
                    </button>
                </div>

                @if ($decryptedData['blood_type'])
                    <div class="flex items-center gap-3 p-3 bg-red-50 dark:bg-red-900/20 rounded-2xl">
                        <x-icon name="droplets" class="w-8 h-8 text-red-500" />
                        <div>
                            <div class="text-[10px] uppercase text-gray-400 font-bold">{{ __('vault.blood_type') }}</div>
                            <div class="text-xl font-black text-red-500">{{ $decryptedData['blood_type'] }}</div>
                        </div>
                    </div>
                @endif

                @if (count($decryptedData['allergies']) > 0)
                    <div class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-2xl">
                        <div class="text-[10px] uppercase text-gray-400 font-bold mb-1.5">{{ __('vault.allergies') }}</div>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($decryptedData['allergies'] as $allergy)
                                <x-badge variant="warning">{{ $allergy }}</x-badge>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (count($decryptedData['medications']) > 0)
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-2xl">
                        <div class="text-[10px] uppercase text-gray-400 font-bold mb-1.5">{{ __('vault.medications') }}</div>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($decryptedData['medications'] as $med)
                                <x-badge variant="primary">{{ $med }}</x-badge>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (count($decryptedData['conditions']) > 0)
                    <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-2xl">
                        <div class="text-[10px] uppercase text-gray-400 font-bold mb-1.5">{{ __('vault.conditions') }}</div>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($decryptedData['conditions'] as $cond)
                                <x-badge variant="success">{{ $cond }}</x-badge>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($decryptedData['emergency_notes'])
                    <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-2xl">
                        <div class="text-[10px] uppercase text-gray-400 font-bold mb-1">{{ __('vault.emergency_notes') }}</div>
                        <p class="text-sm">{{ $decryptedData['emergency_notes'] }}</p>
                    </div>
                @endif
            </x-card>
        @endif

    @endif

</div>

@if(!$embedded)
    @endif
</x-mobile-page>
@endif
