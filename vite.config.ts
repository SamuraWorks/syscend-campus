import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.tsx'],
            refresh: true,
        }),
        react(),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
        },
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
        proxy: {
            // Proxy API and Laravel routes to the backend during Vite dev
            '/api': {
                target: process.env.APP_URL || 'http://localhost:8000',
                changeOrigin: true,
                secure: false,
            },
            '/school': {
                target: process.env.APP_URL || 'http://localhost:8000',
                changeOrigin: true,
                secure: false,
            },
            '/sanctum': {
                target: process.env.APP_URL || 'http://localhost:8000',
                changeOrigin: true,
                secure: false,
            },
        },
    },
});
