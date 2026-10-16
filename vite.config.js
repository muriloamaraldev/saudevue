import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');

    return {
        plugins: [
            laravel({
                input: 'resources/js/app.js',
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
        // server: {
        //     // 0.0.0.0 para aceitar conexões vindas da rede local.
        //     host: true,
        //     // Anuncia o HMR pelo host configurável, senão o websocket
        //     // tenta conectar em localhost quando a página é aberta via IP.
        //     hmr: env.VITE_HMR_HOST
        //         ? { host: env.VITE_HMR_HOST }
        //         : true,
        // },
    };
});
