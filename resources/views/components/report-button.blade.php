@props([
    'targetType',
    'targetId',
    'targetLabel',
])

<button
    x-data="{ sent: false }"
    @click="$dispatch('open-report', { type: '{{ $targetType }}', id: '{{ $targetId }}', label: '{{ $targetLabel }}' })"
    @report-sent.window="if ($event.detail.id === '{{ $targetId }}') { sent = true; setTimeout(() => sent = false, 2000) }"
    class="p-2.5 rounded-xl press-feedback -m-1"
    :class="sent ? 'text-success-500' : 'text-gray-400'"
>
    <x-icon name="flag" class="w-5 h-5" />
</button>
