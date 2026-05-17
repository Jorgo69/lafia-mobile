@php use App\Modules\Community\Enums\ProposalType; @endphp

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
        <div class="fixed inset-0 z-50">
            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/40" wire:click="$set('showForm', false)"></div>

            {{-- Sheet : flex-col pour sticky footer --}}
            <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-full max-w-md bg-white dark:bg-gray-900 rounded-t-3xl flex flex-col max-h-modal">

                {{-- Handle + titre (non-scrollable) --}}
                <div class="px-5 pt-3 pb-2 flex-shrink-0">
                    <div class="w-10 h-1 bg-gray-300 dark:bg-gray-700 rounded-full mx-auto mb-3"></div>
                    <h3 class="font-bold text-base text-center">{{ __('community.propose_title') }}</h3>
                </div>

                {{-- Corps scrollable --}}
                <div class="px-5 py-3 space-y-4 overflow-y-auto flex-1">

                    {{-- Type selector --}}
                    <div class="flex gap-2">
                        @foreach(\App\Modules\Community\Enums\ProposalType::cases() as $pt)
                            <button wire:click="$set('type', '{{ $pt->value }}')"
                                    class="flex-1 flex flex-col items-center justify-center gap-1.5 py-3 min-h-16 rounded-2xl text-2xs font-semibold leading-tight text-center transition-colors press-feedback
                                        {{ $type === $pt->value ? 'bg-primary/10 text-primary' : 'bg-gray-100 dark:bg-gray-800 text-gray-500' }}">
                                <x-icon :name="$pt->icon()" class="w-5 h-5 shrink-0" />
                                <span>{{ $pt->label() }}</span>
                            </button>
                        @endforeach
                    </div>

                    {{-- Formulaire dynamique --}}
                    <div class="space-y-3">
                        @if($type === ProposalType::PHARMACY->value)
                            <input type="text" wire:model="name" placeholder="{{ __('community.field_name') }}"
                                   class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-3 min-h-11">
                            <input type="tel" wire:model="phone" placeholder="{{ __('community.field_phone') }}"
                                   class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-3 min-h-11">
                            <input type="text" wire:model="zone" placeholder="{{ __('community.field_zone') }}"
                                   class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-3 min-h-11">

                        @elseif($type === ProposalType::EMERGENCY_CONTACT->value)
                            <input type="text" wire:model="name" placeholder="{{ __('community.field_name') }}"
                                   class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-3 min-h-11">
                            <input type="tel" wire:model="phone" placeholder="{{ __('community.field_phone') }}"
                                   class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-3 min-h-11">
                            <input type="text" wire:model="commune" placeholder="{{ __('community.field_commune') }}"
                                   class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-3 min-h-11">

                        @elseif($type === ProposalType::USSD_CODE->value)
                            <input type="text" wire:model="code" placeholder="{{ __('community.field_code') }}"
                                   class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-3 min-h-11">
                            <select wire:model="operator"
                                    class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-3 min-h-11">
                                <option value="">{{ __('community.field_operator') }}</option>
                                @foreach($operators as $op)
                                    <option value="{{ $op->value }}">{{ $op->label() }}</option>
                                @endforeach
                            </select>
                            <input type="text" wire:model="description" placeholder="{{ __('community.field_description') }}"
                                   class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-3 min-h-11">
                        @endif

                        <input type="text" wire:model="reason" placeholder="{{ __('community.field_reason') }}"
                               class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-3 min-h-11">
                    </div>

                    {{-- Erreurs --}}
                    @error('name') <p class="text-xs text-danger-500">{{ $message }}</p> @enderror
                    @error('phone') <p class="text-xs text-danger-500">{{ $message }}</p> @enderror
                    @error('code') <p class="text-xs text-danger-500">{{ $message }}</p> @enderror

                </div>

                {{-- Footer sticky --}}
                <div class="px-5 py-4 flex gap-3 flex-shrink-0 bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800"
                     style="padding-bottom: calc(1rem + env(safe-area-inset-bottom, 0))">
                    <x-btn variant="primary" wire:click="propose" class="flex-1" loading="propose">
                        {{ __('community.submit') }}
                    </x-btn>
                    <x-btn variant="outline" wire:click="$set('showForm', false)" class="flex-1">
                        {{ __('common.cancel') }}
                    </x-btn>
                </div>

            </div>
        </div>
    @endif
</div>
