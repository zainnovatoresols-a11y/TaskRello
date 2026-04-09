import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        // Existing Laravel paths
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',

        // Your additional paths
        './resources/**/*.js',
        './public/js/**/*.js',
    ],

    theme: {
        extend: {
            // Merge font families (keep Figtree + fallback + Inter if needed)
            fontFamily: {
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
            },

            // Add your custom brand colors
            colors: {
                brand: {
                    DEFAULT: '#0052CC',
                    50:  '#E6F0FF',
                    100: '#CCE0FF',
                    200: '#99C0FF',
                    300: '#66A0FF',
                    400: '#3380FF',
                    500: '#0052CC',
                    600: '#0047B3',
                    700: '#003D99',
                    800: '#003380',
                    900: '#002966',
                },
            },
        },
    },

    // Keep existing plugin
    plugins: [forms],
};