@php $embedded = $this->embedded ?? false; @endphp

@if(!$embedded)

<x-mobile-page :title="__('vault.title')">

    @if(!$isLocked)
        <x-slot:actions>
            <button wire:click="lock" class="text-sm text-danger-500 font-medium flex items-center gap-1.5 min-h-11 px-3 press-feedback rounded-xl">
                <x-icon name="lock" class="w-4 h-4" /> {{ __('vault.lock') }}
            </button>
        </x-slot:actions>
    @endif

    @if(!$authUnlocked)
        <x-auth-gate />
    @else
        <div class="space-y-5">
            @include('livewire.partials.profil-vital-body')
        </div>
    @endif

</x-mobile-page>

@else

<div class="space-y-5">
    @if(!$isLocked)
        <div class="flex justify-end">
            <button wire:click="lock" class="text-sm text-danger-500 font-medium flex items-center gap-1.5 min-h-11 px-3 press-feedback rounded-xl">
                <x-icon name="lock" class="w-4 h-4" /> {{ __('vault.lock') }}
            </button>
        </div>
    @endif

    @include('livewire.partials.profil-vital-body')
</div>

@endif
