import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const apiProxyTarget =
        env.VITE_DEV_SERVER_PROXY ||
        env.APP_URL?.replace(/\/$/, '') ||
        'http://127.0.0.1:8000';

    // When testing from LAN devices (iPhone, etc.)
    // - Vite must listen on 0.0.0.0 (host: true)
    // - Set VITE_HMR_HOST to your Mac LAN IP (e.g. 192.168.1.20)
    const hmrHost = env.VITE_HMR_HOST || 'localhost';
    const hmrProtocol = env.VITE_HMR_PROTOCOL || 'ws';
    const hmrClientPort = env.VITE_HMR_CLIENT_PORT ? Number(env.VITE_HMR_CLIENT_PORT) : undefined;
    const devOrigin = env.VITE_DEV_SERVER_ORIGIN || undefined;

    return {
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
        host: true,
        port: 5174,
        cors: true,
        origin: devOrigin,
        hmr: {
            host: hmrHost,
            protocol: hmrProtocol,
            clientPort: hmrClientPort,
        },
        proxy: {
            '/api': {
                target: apiProxyTarget,
                changeOrigin: true,
                secure: false,
            },
            '/sanctum': {
                target: apiProxyTarget,
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
        define: {
            __VUE_PROD_DEVTOOLS__: false,
            __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: false,
        },
    };
});