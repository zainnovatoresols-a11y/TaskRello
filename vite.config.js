import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js',  'resources/js/echo.js', 'resources/js/board.js', 'resources/js/card.js', 'resources/js/list.js'],
            refresh: true,
        }),
    ],
});
