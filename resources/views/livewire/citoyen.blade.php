<x-mobile-page :title="__('nav.citizen')" :back="true">

    <div class="flex flex-col items-center justify-center min-h-content px-6 text-center space-y-5">

        <div class="w-20 h-20 rounded-full bg-warning-100 dark:bg-warning-900/30 flex items-center justify-center">
            <x-icon name="megaphone" class="w-10 h-10 text-warning-500" />
        </div>

        <div class="space-y-2">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">229 Citoyen</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 max-w-xs mx-auto leading-relaxed">
                {{ __('nav.citizen_desc') }}
            </p>
        </div>

        {{-- Catégories à venir --}}
        <div class="w-full max-w-sm space-y-2 text-left">
            @foreach([
                ['icon' => 'road', 'label' => 'Infrastructure', 'desc' => 'Route, pont, éclairage public'],
                ['icon' => 'shield-alert', 'label' => 'Sécurité', 'desc' => 'Insécurité, vol, agression'],
                ['icon' => 'droplets', 'label' => 'Eau & Électricité', 'desc' => 'Coupures, fuites, pannes'],
                ['icon' => 'trash-2', 'label' => 'Environnement', 'desc' => 'Déchets, pollution, inondation'],
                ['icon' => 'building-2', 'label' => 'Services publics', 'desc' => 'Mairie, hôpital, école'],
            ] as $item)
            <div class="flex items-center gap-3 bg-white dark:bg-gray-800 rounded-2xl px-4 py-3 opacity-50">
                <div class="w-9 h-9 rounded-xl bg-warning-50 dark:bg-warning-900/20 flex items-center justify-center shrink-0">
                    <x-icon :name="$item['icon']" class="w-5 h-5 text-warning-500" />
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $item['label'] }}</p>
                    <p class="text-xs text-gray-400">{{ $item['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

        <div class="bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-800 rounded-2xl px-4 py-3 w-full max-w-sm">
            <p class="text-xs text-warning-700 dark:text-warning-400 font-medium text-center">
                Bientôt disponible — signalements GPS, photo, confirmation communautaire
            </p>
        </div>

    </div>

</x-mobile-page>
