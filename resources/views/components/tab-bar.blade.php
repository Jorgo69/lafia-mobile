@php
    $tabs = [
        ['href' => '/', 'match' => '/', 'icon' => 'siren', 'label' => __('nav.sos'), 'color' => 'text-red-500'],
        ['href' => '/ussd', 'match' => 'ussd', 'icon' => 'hashtag', 'label' => __('nav.ussd'), 'color' => 'text-blue-500'],
        ['href' => '/pharmacies', 'match' => 'pharmacies', 'icon' => 'building-storefront', 'label' => __('nav.pharma'), 'color' => 'text-green-500'],
        ['href' => '/profil-vital', 'match' => 'profil-vital', 'icon' => 'heart-pulse', 'label' => __('nav.health'), 'color' => 'text-emerald-500'],
        ['href' => '/cercle', 'match' => 'cercle', 'icon' => 'users', 'label' => __('nav.circle'), 'color' => 'text-indigo-500'],
    ];
@endphp

<nav class="tab-bar">
    <div class="flex items-stretch max-w-md mx-auto">
        @foreach ($tabs as $tab)
            @php
                $active = $tab['match'] === '/' ? request()->is('/') : request()->is($tab['match'] . '*');
            @endphp
            <a href="{{ $tab['href'] }}" wire:navigate
               class="flex-1 flex flex-col items-center justify-center gap-0.5 py-2 press-feedback
                   {{ $active ? $tab['color'] : 'text-gray-400 dark:text-gray-600' }}">
                <x-icon :name="$tab['icon']" class="w-6 h-6" />
                <span class="text-[11px] font-semibold leading-tight">{{ $tab['label'] }}</span>
            </a>
        @endforeach
    </div>
</nav>
