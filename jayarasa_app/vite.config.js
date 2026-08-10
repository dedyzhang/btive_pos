import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    // server: {
    //     // host: '0.0.0.0',
    //     // hmr: {
    //     //     host: '192.168.100.190' // Your local machine's IP address
    //     // },
    //     // cors: {
    //     //     origin: 'http://192.168.100.190:8000', // The URL where your Laravel app is running (e.g., http://mylaravel.test or http://192.168.1.10:8000)
    //     //     credentials: true,
    //     // },
    //     watch: {
    //         ignored: ['**/storage/framework/views/**'],
    //     },
    // },
    build: {
        chunkSizeWarningLimit: 3072, // 3MB
        assetsInlineLimit: 0,
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
