/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './index.html',
        './src/**/*.{vue,js,ts,jsx,tsx}',
        // Excluir el módulo admin para evitar conflictos
        '!./src/modules/admin/**/*.{vue,js,ts,jsx,tsx}',
    ],
    // Opcional: usar prefijo para evitar conflictos totalmente
    prefix: 'tw-',
    theme: {
        extend: {},
        screens: {
            sm: '576px',
            md: '768px',
            lg: '992px',
            xl: '1200px',
            '2xl': '1920px',
        },
    },
    plugins: [],
};
