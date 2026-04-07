import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
    build: {
        // Optimize chunk size
        chunkSizeWarningLimit: 800,
        rollupOptions: {
            output: {
                manualChunks: {
                    // Separate ApexCharts into its own chunk
                    charts: ['apexcharts'],
                    // Vendor chunk for node_modules
                    vendor: ['alpinejs', 'flowbite', 'axios'],
                },
            },
        },
        // CSS optimization
        cssCodeSplit: true,
        // Asset optimization
        assetsInlineLimit: 4096,
    },
    // Optimize dependencies
    optimizeDeps: {
        include: ['alpinejs', 'flowbite'],
        exclude: ['apexcharts'], // Lazy loaded
    },
});
