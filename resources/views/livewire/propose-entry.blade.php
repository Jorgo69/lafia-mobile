<div>
    {{-- Bouton "+" flottant --}}
    @if(!$showForm)
        <button wire:click="$set('showForm', true)"
                class="fixed bottom-24 right-4 z-40 w-14 h-14 rounded-full bg-primary text-white shadow-lg
                       flex items-center justify-center press-feedback active:scale-90 transition-transform">
            <x-icon name="plus" class="w-6 h-6" />
        </button>
    @endif

    {{-- Bottom sheet formulaire --}}
    @if($showForm)
        <div class="fixed inset-0 z-50" x-data="{ open: true }" x-show="open" x-cloak>
            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/40" wire:click="$set('showForm', false)"></div>

            {{-- Sheet --}}
            <div class="absolute bottom-0 inset-x-0 bg-white dark:bg-gray-900 rounded-t-3xl p-5 pb-24 space-y-4
                        max-h-[90vh] overflow-y-auto
                        animate-slide-up">

                {{-- Handle --}}
                <div class="w-10 h-1 bg-gray-300 dark:bg-gray-700 rounded-full mx-auto"></div>

                <h3 class="font-bold text-base text-center">{{ __('community.propose_title') }}</h3>

                {{-- Type selector --}}
                <div class="flex gap-2">
                    @foreach(\App\Modules\Community\Enums\ProposalType::cases() as $pt)
                        <button wire:click="$set('type', '{{ $pt->value }}')"
                                class="flex-1 flex flex-col items-center justify-center gap-1.5 py-3 min-h-[64px] rounded-2xl text-[11px] font-semibold leading-tight text-center transition-colors press-feedback
                                    {{ $type === $pt->value ? 'bg-primary/10 text-primary' : 'bg-gray-100 dark:bg-gray-800 text-gray-500' }}">
                            <x-icon :name="$pt->icon()" class="w-5 h-5 shrink-0" />
                            <span>{{ $pt->label() }}</span>
                        </button>
                    @endforeach
                </div>

                {{-- Formulaire dynamique --}}
                <div class="space-y-3">
                    @if($type === 'pharmacy')
                        <input type="text" wire:model="name" placeholder="{{ __('community.field_name') }}"
                               class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-3 min-h-[44px]">
                        <input type="tel" wire:model="phone" placeholder="{{ __('community.field_phone') }}"
                               class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-3 min-h-[44px]">
                        <input type="text" wire:model="zone" placeholder="{{ __('community.field_zone') }}"
                               class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-3 min-h-[44px]">

                    @elseif($type === 'emergency_contact')
                        <input type="text" wire:model="name" placeholder="{{ __('community.field_name') }}"
                               class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-3 min-h-[44px]">
                        <input type="tel" wire:model="phone" placeholder="{{ __('community.field_phone') }}"
                               class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-3 min-h-[44px]">
                        <input type="text" wire:model="commune" placeholder="{{ __('community.field_commune') }}"
                               class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-3 min-h-[44px]">

                    @elseif($type === 'ussd_code')
                        <input type="text" wire:model="code" placeholder="{{ __('community.field_code') }}"
                               class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-3 min-h-[44px]">
                        <select wire:model="operator"
                                class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-3 min-h-[44px]">
                            <option value="">{{ __('community.field_operator') }}</option>
                            <option value="mtn">MTN</option>
                            <option value="moov">Moov</option>
                            <option value="celtiis">Celtiis</option>
                        </select>
                        <input type="text" wire:model="description" placeholder="{{ __('community.field_description') }}"
                               class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-3 min-h-[44px]">
                    @endif

                    <input type="text" wire:model="reason" placeholder="{{ __('community.field_reason') }}"
                           class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-3 min-h-[44px]">
                </div>

                {{-- Actions --}}
                <div class="flex gap-3">
                    <x-btn variant="primary" wire:click="propose" class="flex-1" loading="propose">
                        {{ __('community.submit') }}
                    </x-btn>
                    <x-btn variant="outline" wire:click="$set('showForm', false)" class="flex-1">
                        {{ __('common.cancel') }}
                    </x-btn>
                </div>

                {{-- Erreurs --}}
                @error('name') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                @error('phone') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                @error('code') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
        </div>
    @endif
</div>
