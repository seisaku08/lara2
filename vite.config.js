import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        hmr: {
            host: 'localhost',
        },
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        // vue(),
    ],
    resolve: {
        alias: {
        vue: 'vue/dist/vue.esm-bundler.js',
        // @@@ ↓追記
        '$': 'jQuery',
        },
        },
});
