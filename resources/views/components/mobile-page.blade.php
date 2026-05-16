@props([
    'title' => '',
    'back' => false,
    'actions' => null,
])

<div class="mobile-page bg-surface-muted dark:bg-surface-dark-muted text-surface-dark dark:text-gray-200">

    {{-- Header --}}
    <x-mobile-header :title="$title" :back="$back">
        @if($actions)
            {{ $actions }}
        @endif
    </x-mobile-header>

    {{-- Content --}}
    <div class="mobile-content page-enter">
        <div class="page-padding">
            {{ $slot }}
        </div>
    </div>

</div>
