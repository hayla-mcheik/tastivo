import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
    ],
    resolve: {
        // alias: {
        //     '@' : fileURLToPath(new URL ('./src' , import.meta.url))
        // }
    },
    server : {
        host: '192.168.1.9', // 👈 allows access from other devices
        proxy : {
            '/api' : {
                target : "http://127.0.0.1:8000/",
                changeOrigin : true,
                headers : {
                    Accept : "application/json",
                    "Content-Type" : "application/json",
                },
            },
        },
    },
});
