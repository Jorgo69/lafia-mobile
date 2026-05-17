@php $embedded = $this->embedded ?? false; @endphp

@if(!$embedded)

<x-mobile-page :title="__('circle.title')">
    @if(!$authUnlocked)
        <x-auth-gate />
    @else
        <div class="space-y-5">
            @include('livewire.partials.cercle-confiance-body')
        </div>
    @endif
</x-mobile-page>

@else

<div class="space-y-5">
    @include('livewire.partials.cercle-confiance-body')
</div>

@endif
