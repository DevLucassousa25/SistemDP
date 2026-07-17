import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: '0.0.0.0',   // escuta em todas as interfaces do container, não só 127.0.0.1
        port: 5173,
        strictPort: true,
        hmr: {
            host: 'localhost', // endereço que o NAVEGADOR usa para o websocket do HMR
        },
    },
});
