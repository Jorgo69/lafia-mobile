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
                // Bleu — actions principales, liens, boutons primaires
                primary: {
                    50:  '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    300: '#93c5fd',
                    400: '#60a5fa',
                    500: '#3b82f6',
                    600: '#2563eb',
                    700: '#1d4ed8',
                    800: '#1e40af',
                    900: '#1e3a8a',
                    DEFAULT: '#2563eb',
                    light: '#dbeafe',
                },
                // Indigo — sécurité, coffre, vault, biométrie
                secondary: {
                    50:  '#eef2ff',
                    100: '#e0e7ff',
                    200: '#c7d2fe',
                    300: '#a5b4fc',
                    400: '#818cf8',
                    500: '#6366f1',
                    600: '#4f46e5',
                    700: '#4338ca',
                    800: '#3730a3',
                    900: '#312e81',
                    DEFAULT: '#6366f1',
                    light: '#e0e7ff',
                },
                // Rouge — erreurs, danger, fermé, urgence
                danger: {
                    50:  '#fef2f2',
                    100: '#fee2e2',
                    200: '#fecaca',
                    300: '#fca5a5',
                    400: '#f87171',
                    500: '#ef4444',
                    600: '#dc2626',
                    700: '#b91c1c',
                    800: '#991b1b',
                    900: '#7f1d1d',
                    DEFAULT: '#dc2626',
                    light: '#fee2e2',
                },
                // Vert — succès, ouvert, confirmé, pharmacie
                success: {
                    50:  '#ecfdf5',
                    100: '#d1fae5',
                    200: '#a7f3d0',
                    300: '#6ee7b7',
                    400: '#34d399',
                    500: '#10b981',
                    600: '#059669',
                    700: '#047857',
                    800: '#065f46',
                    900: '#064e3b',
                    DEFAULT: '#059669',
                    light: '#d1fae5',
                },
                // Ambre — avertissements, en attente, ne répond pas
                warning: {
                    50:  '#fffbeb',
                    100: '#fef3c7',
                    200: '#fde68a',
                    300: '#fcd34d',
                    400: '#fbbf24',
                    500: '#f59e0b',
                    600: '#d97706',
                    700: '#b45309',
                    800: '#92400e',
                    900: '#78350f',
                    DEFAULT: '#f59e0b',
                    light: '#fef3c7',
                },
                // Couleurs de marque des opérateurs (identité visuelle immuable)
                brand: {
                    mtn:     '#ffcc00',
                    moov:    '#0066ff',
                    celtiis: '#e60000',
                },
                // Surfaces — backgrounds, cartes
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
            fontSize: {
                // Tailles custom non couvertes par les défauts Tailwind
                // (xs=12px, sm=14px, base=16px sont des défauts Tailwind)
                '3xs': ['9px',  { lineHeight: '12px' }], // labels micro (langue)
                '2xs': ['10px', { lineHeight: '14px' }], // labels section, badges compacts
                'body': ['15px', { lineHeight: '22px' }], // corps de liste entre sm et base
            },
            maxHeight: {
                // Hauteurs max pour les bottom sheets et modals
                'sheet': '85vh',
                'modal': '90vh',
                'content': '60vh', // écrans d'état vide
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
};
