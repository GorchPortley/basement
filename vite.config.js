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
        // Bind all interfaces so the dev server is reachable from a container.
        host: '0.0.0.0',
        port: Number(process.env.VITE_PORT) || 5173,
        strictPort: true,
        allowedHosts: true,
        hmr: {
            // Host the browser connects to for HMR. Override for LAN/remote dev.
            host: process.env.VITE_HMR_HOST || 'localhost',
        },
        watch: {
            // Enable when bind-mounted file events don't propagate (some hosts).
            usePolling: process.env.VITE_USE_POLLING === 'true',
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
