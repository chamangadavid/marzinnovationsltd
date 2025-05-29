import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Montserrat', ...defaultTheme.fontFamily.sans], // Add Montserrat
                //sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'primary-teal': '#004d40', // You can remove this if you don't use it anymore
                'highlight-green': '#92e623',
                'button-green': '#28a745',
                // Define your new custom purple here
                'hero-purple': '#6A0DAD', // Example: a common shade of purple
                // Or a darker one:
                // 'hero-purple': '#4B0082', // Indigo

                'gold': {
                    light: '#F7E7C5',    // For the icon box background
                    DEFAULT: '#D4AF37',  // For the icon color and heading underline
                    dark: '#A98D33',     // An optional darker shade if you need it
                },
            },
            // colors: {
            //     // Define your custom colors here
            //     'primary-teal': '#004d40',
            //     'highlight-green': '#92e623',
            //     'button-green': '#28a745',
            //     // You can name them whatever makes sense for your project
            // },
        },
    },

    plugins: [forms],
};
