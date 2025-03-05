import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'; // Asegúrate de importar el plugin
import tailwindcss from '@tailwindcss/vite'


export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(), // Agrega el plugin aquí
        tailwindcss(),

    ],
});
