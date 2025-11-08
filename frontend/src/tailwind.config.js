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
                'background-dark': '#1E1E1E',
                'surface-light': '#F5F5F5',
                'surface-dark': '#1F1F1F',
                'text-light': '#1A1A1A',
                'text-dark': '#E5E5E5',
                'muted-light': '#666666',
                'muted-dark': '#999999',
                'naranja': '#FF5000',
                'muted-border': '#e5e7eb',
                'verde': '#278427',
                'azul-oscuro': '#334155',
                'rojo': '#cb2222',
                'rojo-oscuro': '#981412',
                'verde-claro': '#E2F6E2',
            },
            fontFamily: {
                display: ['Roboto', 'sans-serif'],
                oswald: ['Oswald', 'sans-serif'],
                rubik: ['Rubik', 'sans-serif'],
            },
            borderRadius: {
                DEFAULT: '0.5rem',
            },
            screens: {
                sm: '576px',
                md: '768px',
                lg: '992px',
                xl: '1200px',
                '2xl': '1620px',
            },
        },
    },
};
