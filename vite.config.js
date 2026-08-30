import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/js/react/admin-dashboard.jsx',
                'resources/js/react/student-portal.jsx',
                'resources/js/react/enrollment-form.jsx',
                'resources/js/react/about-page.jsx',
                'resources/js/react/academics-page.jsx',
                'resources/js/react/admission-page.jsx',
                'resources/js/react/news-page.jsx',
                'resources/js/react/contact-page.jsx'
            ],
            refresh: true,
        }),
        tailwindcss(),
        react(),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: (id) => {
                    if (id.includes('react') || id.includes('react-dom')) {
                        return 'react-vendor';
                    }
                    if (id.includes('recharts')) {
                        return 'react-charts';
                    }
                    if (id.includes('react-hook-form')) {
                        return 'react-forms';
                    }
                }
            }
        }
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
