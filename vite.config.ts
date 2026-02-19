import { createRequire } from 'node:module';
import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

const require = createRequire(import.meta.url);

function pwaPlugin() {
    try {
        const { VitePWA } = require('vite-plugin-pwa');
        return VitePWA({
            registerType: 'autoUpdate',
            injectRegister: null,
            outDir: 'public',
            manifest: {
                name: 'Film Night',
                short_name: 'Film Night',
                description: 'Weekly movie picks — Thursday & Friday',
                theme_color: '#0a0a0a',
                background_color: '#0a0a0a',
                display: 'standalone',
                scope: '/',
                start_url: '/',
                icons: [
                    {
                        src: '/android/android-launchericon-48-48.png',
                        sizes: '48x48',
                        type: 'image/png',
                    },
                    {
                        src: '/android/android-launchericon-72-72.png',
                        sizes: '72x72',
                        type: 'image/png',
                    },
                    {
                        src: '/android/android-launchericon-96-96.png',
                        sizes: '96x96',
                        type: 'image/png',
                    },
                    {
                        src: '/android/android-launchericon-144-144.png',
                        sizes: '144x144',
                        type: 'image/png',
                    },
                    {
                        src: '/android/android-launchericon-192-192.png',
                        sizes: '192x192',
                        type: 'image/png',
                        purpose: 'any',
                    },
                    {
                        src: '/android/android-launchericon-512-512.png',
                        sizes: '512x512',
                        type: 'image/png',
                        purpose: 'maskable',
                    },
                    {
                        src: '/favicon.svg',
                        sizes: 'any',
                        type: 'image/svg+xml',
                        purpose: 'any',
                    },
                ],
            },
            workbox: {
                globDirectory: 'public',
                globPatterns: [
                    'build/**/*.{js,css,ico,png,svg,woff2}',
                    'android/*.png',
                    'ios/*.png',
                    'favicon.ico',
                    'favicon.svg',
                    'apple-touch-icon.png',
                ],
                navigateFallback: null,
            },
        });
    } catch {
        return null;
    }
}

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.tsx'],
            ssr: 'resources/js/ssr.tsx',
            refresh: true,
        }),
        react({
            babel: {
                plugins: ['babel-plugin-react-compiler'],
            },
        }),
        tailwindcss(),
        wayfinder({
            formVariants: true,
        }),
        pwaPlugin(),
    ].filter(Boolean),
    esbuild: {
        jsx: 'automatic',
    },
});
