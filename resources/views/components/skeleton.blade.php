@props(['lines' => 3, 'avatar' => false])

<div class="animate-pulse flex gap-4">
    @if ($avatar)
        <div class="rounded-full bg-gray-200 dark:bg-gray-700 h-10 w-10 shrink-0"></div>
    @endif
    <div class="flex-1 space-y-3 py-1">
        @for ($i = 0; $i < $lines; $i++)
            <div class="h-2.5 bg-gray-200 dark:bg-gray-700 rounded" style="width: {{ $i === 0 ? '75%' : ($i === $lines - 1 ? '40%' : '90%') }}"></div>
        @endfor
    </div>
</div>
