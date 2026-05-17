@php use App\Livewire\Enums\CoffreTab; @endphp

<x-mobile-page :title="__('nav.vault')">

    @if(!$authUnlocked)
        <x-auth-gate />
    @else

        {{-- Onglets internes --}}
        <div class="flex gap-2 mb-5">
            @foreach($tabs as $t)
                <button wire:click="setTab('{{ $t->value }}')"
                        class="flex-1 flex items-center justify-center gap-2 py-3 min-h-[44px] rounded-2xl text-sm font-semibold transition-colors press-feedback
                            {{ $tab === $t ? $t->activeColor() : 'bg-gray-100 dark:bg-gray-800 text-gray-500' }}">
                    <x-icon :name="$t->icon()" class="w-5 h-5" />
                    {{ $t->label() }}
                </button>
            @endforeach
        </div>

        {{-- Contenu --}}
        @if($tab === CoffreTab::SANTE)
            <livewire:profil-vital :embedded="true" :key="'profil-vital'" />
        @elseif($tab === CoffreTab::GARDIENS)
            <livewire:cercle-confiance :embedded="true" :key="'cercle-confiance'" />
        @endif

    @endif

</x-mobile-page>
