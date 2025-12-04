import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans], // Dùng font Inter cho hiện đại
            },
            colors: {
                // Định nghĩa màu thương hiệu Nexus
                nexus: {
                    50: '#f0f9ff',
                    100: '#e0f2fe',
                    500: '#0ea5e9', // Màu chính (Sky Blue)
                    600: '#0284c7', // Màu hover
                    900: '#0c4a6e',
                },
                'reddit-gray': '#dae0e6', // Màu nền đặc trưng của Reddit
            }
        },
    },

    plugins: [forms],
};
