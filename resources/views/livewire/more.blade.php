<x-mobile-page :title="__('nav.more')">
    <div class="space-y-3">

        {{-- 229 Citoyen --}}
        <a href="/citoyen" wire:navigate class="flex items-center gap-4 bg-white dark:bg-gray-800 rounded-2xl px-4 py-4 min-h-16 press-feedback">
            <div class="w-11 h-11 rounded-full bg-warning-100 dark:bg-warning-900/30 flex items-center justify-center shrink-0">
                <x-icon name="megaphone" class="w-5 h-5 text-warning-600" />
            </div>
            <div class="flex-1">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('nav.citizen') }}</p>
                <p class="text-xs text-gray-400">{{ __('nav.citizen_desc') }}</p>
            </div>
            <x-icon name="chevron-right" class="w-4 h-4 text-gray-400 shrink-0" />
        </a>

        {{-- Conseils pratiques --}}
        <a href="/conseils" wire:navigate class="flex items-center gap-4 bg-white dark:bg-gray-800 rounded-2xl px-4 py-4 min-h-16 press-feedback">
            <div class="w-11 h-11 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center shrink-0">
                <x-icon name="lightbulb" class="w-5 h-5 text-primary-600" />
            </div>
            <div class="flex-1">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('nav.tips') }}</p>
                <p class="text-xs text-gray-400">{{ __('nav.tips_desc') }}</p>
            </div>
            <x-icon name="chevron-right" class="w-4 h-4 text-gray-400 shrink-0" />
        </a>

        {{-- Parametres --}}
        <a href="/settings" wire:navigate class="flex items-center gap-4 bg-white dark:bg-gray-800 rounded-2xl px-4 py-4 min-h-16 press-feedback">
            <div class="w-11 h-11 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center shrink-0">
                <x-icon name="cog" class="w-5 h-5 text-gray-600 dark:text-gray-400" />
            </div>
            <div class="flex-1">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('common.settings') }}</p>
                <p class="text-xs text-gray-400">{{ __('nav.settings_desc') }}</p>
            </div>
            <x-icon name="chevron-right" class="w-4 h-4 text-gray-400 shrink-0" />
        </a>

        {{-- A propos --}}
        <div class="text-center pt-8 pb-4">
            <p class="text-xs text-gray-400">Lafia v1.0.0</p>
            <p class="text-xs text-gray-400">AGPL-3.0 — Benin</p>
        </div>

    </div>
</x-mobile-page>
