import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Marca SaúdeVue: verde como cor primária de toda a interface.
                brand: {
                    50: '#ecfdf5',
                    100: '#d1fae5',
                    200: '#a7f3d0',
                    300: '#6ee7b7',
                    400: '#34d399',
                    500: '#10b981',
                    600: '#059669',
                    700: '#047857',
                    800: '#065f46',
                    900: '#064e3b',
                },
            },
            boxShadow: {
                card: '0 1px 2px 0 rgb(6 78 59 / 0.04), 0 1px 3px 0 rgb(6 78 59 / 0.06)',
                'card-md':
                    '0 1px 2px 0 rgb(6 78 59 / 0.05), 0 4px 12px -2px rgb(6 78 59 / 0.08)',
                'card-lg':
                    '0 2px 4px 0 rgb(6 78 59 / 0.04), 0 12px 32px -4px rgb(6 78 59 / 0.12)',
            },
            backgroundImage: {
                'brand-glow':
                    'radial-gradient(60rem 40rem at 85% -10%, rgb(16 185 129 / 0.12), transparent 60%), radial-gradient(45rem 30rem at 0% 110%, rgb(20 184 166 / 0.10), transparent 55%)',
            },
        },
    },

    plugins: [forms],
};
