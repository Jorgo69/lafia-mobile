@php
    $tabs = [
        ['href' => '/', 'match' => [''], 'icon' => 'siren', 'label' => __('nav.sos'), 'color' => 'text-danger-500'],
        ['href' => '/ussd', 'match' => ['ussd*'], 'icon' => 'hashtag', 'label' => __('nav.ussd'), 'color' => 'text-primary-500'],
        ['href' => '/pharmacies', 'match' => ['pharmacies*'], 'icon' => 'building-storefront', 'label' => __('nav.pharma'), 'color' => 'text-success-500'],
        ['href' => '/coffre', 'match' => ['coffre*', 'profil-vital*', 'cercle*'], 'icon' => 'lock-keyhole', 'label' => __('nav.vault'), 'color' => 'text-primary'],
        ['href' => '/plus', 'match' => ['plus*', 'settings*', 'citoyen*', 'conseils*'], 'icon' => 'grid-2x2', 'label' => __('nav.more'), 'color' => 'text-gray-600 dark:text-gray-300'],
    ];
@endphp

<nav class="tab-bar">
    <div class="flex items-stretch max-w-md mx-auto">
        @foreach ($tabs as $tab)
            @php
                $active = collect($tab['match'])->contains(fn($pattern) =>
                    $pattern === '' ? request()->is('/') : request()->is($pattern)
                );
            @endphp
            <a href="{{ $tab['href'] }}" wire:navigate
               class="flex-1 flex flex-col items-center justify-center gap-0.5 py-2.5 min-h-14 press-feedback
                   {{ $active ? $tab['color'] : 'text-gray-400 dark:text-gray-600' }}">
                <div class="relative">
                    <x-icon :name="$tab['icon']" class="{{ $active ? 'w-7 h-7' : 'w-6 h-6' }} transition-all" />
                    @if($active)
                        <div class="absolute -bottom-1.5 left-1/2 -translate-x-1/2 w-4 h-1 rounded-full bg-current"></div>
                    @endif
                </div>
                <span class="text-2xs {{ $active ? 'font-bold' : 'font-semibold' }} leading-tight">{{ $tab['label'] }}</span>
            </a>
        @endforeach
    </div>
</nav>
