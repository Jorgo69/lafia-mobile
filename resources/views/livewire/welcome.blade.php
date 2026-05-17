<div class="flex flex-col min-h-screen px-6 py-10 safe-area-top safe-area-bottom bg-white">

    {{-- Logo + Nom --}}
    <div class="text-center pt-10 pb-8">
        <div class="w-20 h-20 mx-auto mb-4 rounded-3xl bg-success flex items-center justify-center">
            <x-icon name="heart-pulse" class="w-10 h-10 text-white" />
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Lafia</h1>
        <p class="text-sm text-gray-500 mt-2">Secours & Protection civile</p>
    </div>

    {{-- Choix de langue --}}
    <div class="flex-1 flex flex-col justify-center">
        <p class="text-center text-sm text-gray-500 mb-5">Choisissez votre langue</p>

        <div class="grid grid-cols-2 gap-3">
            @foreach ($locales as $code => $info)
                <button
                    wire:click="selectLocale('{{ $code }}')"
                    class="relative flex flex-col items-center justify-center gap-1.5 min-h-[72px] py-4 rounded-2xl border-2 transition-all press-feedback
                        {{ $selectedLocale === $code
                            ? 'border-primary bg-primary/5'
                            : 'border-gray-100 bg-white' }}"
                >
                    <span class="text-base font-bold {{ $selectedLocale === $code ? 'text-primary' : 'text-gray-800' }}">
                        {{ $info['label'] }}
                    </span>
                    <span class="text-xs {{ $selectedLocale === $code ? 'text-primary/70' : 'text-gray-400' }}">
                        {{ $info['native'] }}
                    </span>

                    @if ($selectedLocale === $code)
                        <div class="absolute top-2 right-2 w-5 h-5 rounded-full bg-primary flex items-center justify-center">
                            <x-icon name="check" class="w-3 h-3 text-white" />
                        </div>
                    @endif
                </button>
            @endforeach
        </div>
    </div>

    {{-- Bouton continuer --}}
    <div class="pt-6 pb-4">
        <x-btn variant="primary" wire:click="start" class="w-full" size="lg" loading="start">
            Commencer
        </x-btn>
        <p class="text-xs text-gray-400 text-center mt-3">Benin - AGPL-3.0</p>
    </div>

</div>
