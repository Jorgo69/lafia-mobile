/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './app/**/*.php',
        './vendor/livewire/livewire/src/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    50: '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    300: '#93c5fd',
                    400: '#60a5fa',
                    500: '#2563eb',
                    600: '#1d4ed8',
                    700: '#1e3a8a',
                    DEFAULT: '#2563eb',
                    light: '#dbeafe',
                },
                danger: {
                    50: '#fef2f2',
                    100: '#fee2e2',
                    500: '#dc2626',
                    600: '#b91c1c',
                    700: '#991b1b',
                    DEFAULT: '#dc2626',
                    light: '#fee2e2',
                },
                success: {
                    50: '#ecfdf5',
                    100: '#d1fae5',
                    500: '#059669',
                    600: '#047857',
                    700: '#065f46',
                    DEFAULT: '#059669',
                    light: '#d1fae5',
                },
                warning: {
                    50: '#fff7ed',
                    100: '#ffedd5',
                    500: '#ea580c',
                    600: '#c2410c',
                    DEFAULT: '#ea580c',
                    light: '#ffedd5',
                },
                surface: {
                    DEFAULT: '#ffffff',
                    muted: '#f8fafc',
                    dark: '#0f172a',
                    'dark-card': '#1e293b',
                    'dark-muted': '#0f172a',
                },
            },
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
};
