@props(['key', 'class' => ''])

@php
    $audioService = app(\App\Services\AudioGuide\AudioGuideService::class);
    $text = $audioService->getGuideText($key);
    $audioPath = $audioService->getAudioPath($key);
@endphp

@if ($text)
    <button
        type="button"
        onclick="playAudioGuide(this)"
        data-audio-path="{{ $audioPath ?? '' }}"
        data-audio-text="{{ $text }}"
        class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-lafia-blue-light text-lafia-blue hover:bg-lafia-blue hover:text-white transition-colors active:scale-90 {{ $class }}"
        title="{{ __('audio.listen') }}"
    >
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
            <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3A4.5 4.5 0 0014 8.5v7a4.49 4.49 0 002.5-3.5zM14 3.23v2.06a6.51 6.51 0 010 13.42v2.06A8.5 8.5 0 0014 3.23z"/>
        </svg>
    </button>
@endif
