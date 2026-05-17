<x-mobile-page :title="__('nav.vault')">
    @if(!$authUnlocked)
        <x-auth-gate />
    @else

    {{-- Onglets internes --}}
    <div class="flex gap-2 mb-5">
        <button wire:click="setTab('sante')"
                class="flex-1 flex items-center justify-center gap-2 py-3 min-h-[44px] rounded-2xl text-sm font-semibold transition-colors press-feedback
                    {{ $tab === 'sante' ? 'bg-primary/10 text-primary' : 'bg-gray-100 dark:bg-gray-800 text-gray-500' }}">
            <x-icon name="heart-pulse" class="w-5 h-5" />
            {{ __('nav.health') }}
        </button>
        <button wire:click="setTab('gardiens')"
                class="flex-1 flex items-center justify-center gap-2 py-3 min-h-[44px] rounded-2xl text-sm font-semibold transition-colors press-feedback
                    {{ $tab === 'gardiens' ? 'bg-indigo-500/10 text-indigo-500' : 'bg-gray-100 dark:bg-gray-800 text-gray-500' }}">
            <x-icon name="users" class="w-5 h-5" />
            {{ __('nav.guardians') }}
        </button>
    </div>

    {{-- Contenu --}}
    @if($tab === 'sante')
        <livewire:profil-vital :embedded="true" :key="'profil-vital'" />
    @else
        <livewire:cercle-confiance :embedded="true" :key="'cercle-confiance'" />
    @endif

    @endif
</x-mobile-page>
