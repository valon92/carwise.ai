import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['vue', 'vue-router'],
                    utils: ['axios', 'lodash'],
                    ui: ['@headlessui/vue', '@heroicons/vue']
                }
            }
        },
        chunkSizeWarningLimit: 1000,
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
                pure_funcs: ['console.log', 'console.info', 'console.debug'],
                passes: 2
            },
            mangle: {
                safari10: true
            }
        },
        sourcemap: false,
        reportCompressedSize: false,
        target: 'es2015',
        cssCodeSplit: true,
        assetsInlineLimit: 4096
    },
    server: {
        host: '127.0.0.1',
        port: 5174,
        hmr: {
            host: '127.0.0.1',
            port: 5174,
        },
        proxy: {
            '/api': {
                target: 'http://127.0.0.1:8000',
                changeOrigin: true,
                secure: false,
            },
        },
    },
    resolve: {
        alias: {
            '@': '/resources/js',
            'vue': 'vue/dist/vue.esm-bundler.js',
        },
    },
    optimizeDeps: {
        include: [
            'vue', 
            'vue-router', 
            'axios',
            '@headlessui/vue',
            '@heroicons/vue/24/outline',
            '@heroicons/vue/24/solid'
        ],
        exclude: ['@vite/client', '@vite/env']
    },
    // Add performance configurations
    define: {
        __VUE_PROD_DEVTOOLS__: false,
        __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: false
    }
});