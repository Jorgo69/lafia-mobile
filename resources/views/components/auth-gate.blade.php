{{-- System auth gate — triggers phone's native lock (face/fingerprint/PIN/pattern) --}}
<div
    x-data="{
        checking: true,
        failed: false,
        async promptAuth() {
            this.failed = false
            try {
                if (window.Native && window.Native.system) {
                    const canPrompt = await window.Native.system.canPromptTouchID()
                    if (canPrompt) {
                        const success = await window.Native.system.promptTouchID('{{ __('vault.auth_reason') }}')
                        if (success) {
                            this.checking = false
                            $wire.authSuccess()
                            return
                        }
                    }
                }
            } catch (e) {}

            // NativePHP not available or auth failed
            this.checking = false
            this.failed = true
        },
        skip() {
            $wire.authUnavailable()
        }
    }"
    x-init="promptAuth()"
    class="flex flex-col items-center justify-center min-h-content px-6"
>
    {{-- Checking --}}
    <div x-show="checking" class="text-center">
        <div class="w-16 h-16 rounded-full bg-secondary-100 dark:bg-secondary-900/30 flex items-center justify-center mx-auto mb-4">
            <x-icon name="lock" class="w-8 h-8 text-secondary-500 animate-pulse" />
        </div>
        <p class="text-sm text-gray-500">{{ __('vault.checking_auth') }}</p>
    </div>

    {{-- Failed / retry --}}
    <div x-show="!checking && failed" x-cloak class="text-center">
        <div class="w-16 h-16 rounded-full bg-danger-100 dark:bg-danger-900/30 flex items-center justify-center mx-auto mb-4">
            <x-icon name="lock" class="w-8 h-8 text-danger-500" />
        </div>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">{{ __('vault.auth_failed') }}</p>

        <button
            @click="promptAuth()"
            class="bg-secondary-500 text-white px-8 py-3 rounded-2xl font-semibold press-feedback mb-3"
        >
            {{ __('vault.auth_retry') }}
        </button>
    </div>
</div>
