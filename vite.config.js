import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/sass/animateBorder.scss',
                'resources/js/app.js',
                'resources/css/app.css',
                'resources/js/admin.js',
                'resources/css/style.css',
                'resources/css/quill_val.css',
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
        }
    },
    build: {
        rollupOptions: {
          external: ['perfect-scrollbar']
        }
      }
});
