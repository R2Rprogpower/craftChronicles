import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/scss/bootstrap.scss',
                'resources/scss/icons.scss',
                'resources/scss/app.scss',
                'resources/js/app.js',
                'resources/js/brand-platform/main.jsx',
            ],
            refresh: true,
        }),
        react(),
    ],
});
