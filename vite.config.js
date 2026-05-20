import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        // Заставляем Vite генерировать правильные ссылки для внешних устройств
        hmr: {
            host: 'thread-print-thirty.ngrok-free.dev',
            protocol: 'wss', // Используем защищенные веб-сокеты
        },
    },
});