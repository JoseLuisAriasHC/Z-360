import { fileURLToPath, URL } from 'node:url';

import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import vueDevTools from 'vite-plugin-vue-devtools';

// https://vite.dev/config/
export default defineConfig({
    plugins: [vue(), vueDevTools()],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./src', import.meta.url)),
            '@admin': fileURLToPath(new URL('./src/modules/admin', import.meta.url)),
            '@web': fileURLToPath(new URL('./src/modules/web', import.meta.url)),
            '@shared': fileURLToPath(new URL('./src/shared', import.meta.url)),
        },
    },
});
