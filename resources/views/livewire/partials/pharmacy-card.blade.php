<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-3 relative">
    <div class="flex items-start gap-3">
        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-success-100 dark:bg-success-900/30 flex items-center justify-center">
            <x-icon name="building-storefront" class="w-5 h-5 text-success-600 dark:text-success-400" />
        </div>

        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
                <h4 class="font-semibold text-sm truncate">{{ $pharmacy->name }}</h4>
                @if(!empty($badge))
                    <span class="flex-shrink-0 text-2xs font-bold px-2 py-0.5 rounded-full
                        {{ $badge === __('pharma.on_guard') ? 'bg-success-100 text-success-700 dark:bg-success-900 dark:text-success-400' : 'bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-400' }}">
                        {{ $badge }}
                    </span>
                @endif
            </div>

            @if($pharmacy->neighborhood)
                <p class="text-xs text-gray-500 mt-0.5">{{ $pharmacy->neighborhood }}, {{ $pharmacy->city }}</p>
            @endif

            @if(isset($distance))
                <p class="text-xs font-medium text-success-600 mt-0.5">{{ number_format($distance, 1) }} {{ __('pharma.km') }}</p>
            @endif
        </div>

        <div class="flex items-center gap-1.5">
            {{-- Report button --}}
            <x-report-button
                target-type="pharmacy"
                :target-id="$pharmacy->slug"
                :target-label="$pharmacy->name"
            />

            {{-- Call button --}}
            @if($pharmacy->phone)
                <a href="tel:{{ $pharmacy->phone }}" class="flex-shrink-0 bg-success-500 text-white p-2 rounded-xl press-feedback">
                    <x-icon name="phone" class="w-5 h-5" />
                </a>
            @endif
        </div>
    </div>
</div>
