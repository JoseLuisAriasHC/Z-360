/** @type {import('tailwindcss').Config} */
import PrimeUI from 'tailwindcss-primeui';

export default {
    darkMode: ['selector', '[class*="app-dark"]'],
    content: ['./index.html', './src/**/*.{vue,js,ts,jsx,tsx}'],
    plugins: [PrimeUI],
    theme: {
        extend: {
            colors: {
                primary: '#1A1A1A',
                'background-light': '#FFFFFF',
                'background-dark': '#121212',
                'surface-light': '#F5F5F5',
                'surface-dark': '#1F1F1F',
                'text-light': '#1A1A1A',
                'text-dark': '#E5E5E5',
                'text-muted-light': '#666666',
                'text-muted-dark': '#999999',
            },
            fontFamily: {
                display: ['Roboto', 'sans-serif'],
            },
            borderRadius: {
                DEFAULT: '0.5rem',
            },
            screens: {
                sm: '576px',
                md: '768px',
                lg: '992px',
                xl: '1200px',
                '2xl': '1920px',
            },
        },
    },
};
