@php $isDark = app(\App\Services\Settings\SettingsService::class)->isDarkMode(); @endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <script>
        (function() {
            var ls = localStorage.getItem('dark_mode');
            if (ls === null) { ls = '{{ $isDark ? "1" : "0" }}'; localStorage.setItem('dark_mode', ls); }
            if (ls === '1') document.documentElement.classList.add('dark');

            function applyDark() {
                var dark = localStorage.getItem('dark_mode') === '1';
                document.documentElement.classList.toggle('dark', dark);
                var meta = document.querySelector('meta[name="theme-color"]');
                if (meta) meta.setAttribute('content', dark ? '#111827' : '#ffffff');
            }

            document.addEventListener('livewire:navigated', applyDark);

            window.addEventListener('dark-mode-changed', function(e) {
                var dark = !!(e.detail && e.detail.isDark);
                localStorage.setItem('dark_mode', dark ? '1' : '0');
                applyDark();
            });
        })();
    </script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="theme-color" content="{{ $isDark ? '#111827' : '#ffffff' }}">
    <title>Lafia</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 transition-colors duration-200">

    <div class="max-w-md mx-auto w-full min-h-screen relative">
        {{ $slot }}
    </div>

    <x-toast />
    <x-report-sheet />
    <livewire:sync-undo-notification />
    <livewire:propose-entry />
    <x-tab-bar />

    @livewireScripts
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.hook('request', ({ fail }) => {
                fail(({ status, preventDefault }) => {
                    if (status === 419) {
                        preventDefault();
                        window.location.reload();
                    }
                });
            });
        });
    </script>
</body>
</html>
