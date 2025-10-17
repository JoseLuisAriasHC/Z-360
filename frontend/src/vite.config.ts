import { fileURLToPath, URL } from 'node:url';
import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import vueDevTools from 'vite-plugin-vue-devtools';
import Components from 'unplugin-vue-components/vite';
import { PrimeVueResolver } from 'unplugin-vue-components/resolvers';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        vue(),
        vueDevTools(),
        tailwindcss(),
        Components({
            resolvers: [PrimeVueResolver()],
            dts: true,
        }),
    ],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./src', import.meta.url)),
            '@admin': fileURLToPath(new URL('./src/modules/admin', import.meta.url)),
            '@web': fileURLToPath(new URL('./src/modules/web', import.meta.url)),
            '@shared': fileURLToPath(new URL('./src/shared', import.meta.url)),
        },
    },
    server: {
        host: '0.0.0.0',
        port: 5173,
    },
});