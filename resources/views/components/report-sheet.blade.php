{{-- Global report bottom sheet — one instance in layout --}}
<div
    x-data="{
        open: false,
        target: { type: '', id: '', label: '' },
        types: [
            { value: 'closed', label: '{{ __("common.report_closed") }}', color: 'bg-red-100 dark:bg-red-900/30 text-red-500' },
            { value: 'open', label: '{{ __("common.report_open") }}', color: 'bg-green-100 dark:bg-green-900/30 text-green-500' },
            { value: 'not_responding', label: '{{ __("common.report_not_responding") }}', color: 'bg-amber-100 dark:bg-amber-900/30 text-amber-500' },
            { value: 'wrong_number', label: '{{ __("common.report_wrong_number") }}', color: 'bg-red-50 dark:bg-red-900/20 text-red-400' },
        ],
        report(type) {
            Livewire.dispatch('submit-report', { targetType: this.target.type, targetId: this.target.id, targetLabel: this.target.label, reportType: type });
            window.dispatchEvent(new CustomEvent('report-sent', { detail: { id: this.target.id } }));
            this.open = false;
        }
    }"
    @open-report.window="target = $event.detail; open = true"
>
    {{-- Backdrop --}}
    <div
        x-cloak
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="open = false"
        class="fixed inset-0 bg-black/40 z-50"
    ></div>

    {{-- Panel --}}
    <div
        x-cloak
        x-show="open"
        x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-y-0"
        x-transition:leave-end="translate-y-full"
        class="fixed bottom-0 inset-x-0 z-50 bg-white dark:bg-gray-900 rounded-t-3xl safe-bottom"
    >
        <div class="w-10 h-1 bg-gray-300 dark:bg-gray-600 rounded-full mx-auto mt-3 mb-1"></div>

        <div class="px-5 pt-2 pb-3">
            <p class="text-xs font-bold uppercase text-gray-400 tracking-wide">{{ __('common.report') }}</p>
            <p class="text-sm text-gray-500 mt-0.5" x-text="target.label"></p>
        </div>

        <div class="px-3 pb-3">
            <template x-for="t in types" :key="t.value">
                <button
                    @click="report(t.value)"
                    class="w-full flex items-center gap-4 px-4 py-3.5 rounded-2xl press-feedback active:bg-gray-50 dark:active:bg-gray-800"
                >
                    <span class="w-10 h-10 rounded-full flex items-center justify-center shrink-0" :class="t.color">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <template x-if="t.value === 'closed'"><g><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></g></template>
                            <template x-if="t.value === 'open'"><g><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></g></template>
                            <template x-if="t.value === 'not_responding'"><g><path d="M10.68 13.31a16 16 0 0 0 3.41 2.6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7 2 2 0 0 1 1.72 2v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.42 19.42 0 0 1-3.33-2.67m-2.67-3.34a19.79 19.79 0 0 1-3.07-8.63A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91"/><line x1="22" x2="2" y1="2" y2="22"/></g></template>
                            <template x-if="t.value === 'wrong_number'"><g><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/></g></template>
                        </svg>
                    </span>
                    <span class="text-[15px] font-medium text-gray-900 dark:text-white" x-text="t.label"></span>
                </button>
            </template>
        </div>

        <div class="px-3 pb-24">
            <button
                @click="open = false"
                class="w-full py-3.5 text-center text-sm font-semibold text-gray-500 bg-gray-100 dark:bg-gray-800 rounded-2xl press-feedback min-h-[44px]"
            >
                {{ __('common.cancel') }}
            </button>
        </div>
    </div>
</div>
