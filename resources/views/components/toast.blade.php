<div
    x-data="{
        toasts: [],
        add(e) {
            const id = Date.now()
            this.toasts.push({ id, message: e.detail.message, variant: e.detail.variant || 'success' })
            setTimeout(() => this.toasts = this.toasts.filter(t => t.id !== id), e.detail.duration || 3000)
        },
        colors(variant) {
            return {
                success: 'bg-success-600 text-white',
                error: 'bg-danger-600 text-white',
                warning: 'bg-warning-500 text-white',
                info: 'bg-primary-600 text-white',
            }[variant] || 'bg-gray-800 text-white'
        },
        icons(variant) {
            return {
                success: 'check-circle',
                error: 'x-circle',
                warning: 'alert-triangle',
                info: 'info',
            }[variant] || 'info'
        }
    }"
    @toast.window="add($event)"
    class="fixed top-16 inset-x-4 z-[60] safe-top space-y-2 pointer-events-none"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="max-w-md mx-auto rounded-2xl px-4 py-3 shadow-lg flex items-center gap-2.5 pointer-events-auto"
            :class="colors(toast.variant)"
        >
            <svg class="w-5 h-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <template x-if="toast.variant === 'success'"><g><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></g></template>
                <template x-if="toast.variant === 'error'"><g><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></g></template>
                <template x-if="toast.variant === 'warning'"><g><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/></g></template>
                <template x-if="toast.variant === 'info'"><g><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></g></template>
            </svg>
            <span class="text-sm font-medium" x-text="toast.message"></span>
        </div>
    </template>
</div>
