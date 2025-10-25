import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from 'tailwindcss';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        watch: {
            ignored: [
                '**/node_modules/**',
                '**/public/**',
                '**/resources/**',
                '**/vendor/**',
            ],
        },
        hmr: {
            host: 'localhost',
            protocol: 'ws',
            port: 5173,
        },
    },
    css: {
        postcss: {
            plugins: [tailwindcss],
        },
    },
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        }
    }
});