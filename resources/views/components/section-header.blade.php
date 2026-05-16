@props(['audioKey' => null])

<div class="flex items-center gap-2 mb-2">
    <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $slot }}</h2>
    @if ($audioKey)
        <x-audio-button :key="$audioKey" />
    @endif
    @if (isset($action))
        {{ $action }}
    @endif
</div>
